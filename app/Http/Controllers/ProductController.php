<?php

namespace App\Http\Controllers;

use App\Models\categoryProduct;
use App\Models\Product;
use App\Models\Store;
use App\Models\Store_ditail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Describe;
use Carbon\Carbon;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function acceptAll(Request $request)
    {
	    $user = User::with(['storeUserRelation', 'own'])->where('token', '=' , $request->_token)->firstOrFail();
	    if($user->own->isAdmin) {
		    $products = Product::onlyTrashed()->where('store_id', '!=', null)->get();
			foreach($products as $product) {
				$product->restore();
				Describe::where('describe_id', $product->id)->latest()->firstOrFail()->update([
					'status' => 1,
					'user_geter' => $user->name . ' '. $user->lastname
				]);
			}
	    }
	    return response(['message' => 'accept all is success', 'value' => null]);

	    foreach($user->storeUserRelation as $store_id){
	    	$products = Product::onlyTrashed()->where('store_id', $store_id->store_id)->get();
	    }
	    foreach($products as $product) {
	    	$product->restore();

		Describe::where('describe_id', $product->id)->latest()->firstOrFail()->update([
			'status' => 1,
			'user_geter' => $user->name .' '.$user->lastname
		]);
	    }

	    return response(['message' => 'accept all is success', 'value' => null]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	     if(isset($request->groupAdd)) {
		 $uploadFile = $request->file('file');
		 $fileName = time().$uploadFile->getClientOriginalName();
		 Storage::disk('local')->putFileAs('public/',$uploadFile, $fileName);
       	 	 $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
       	 	 $value = $reader->load('../public/storage/' . $fileName)->getSheet(0)->toArray();
         	Storage::disk('local')->delete("public/" . $fileName);
         	foreach($value as $val) {
                $store_id =  Store::where('name', '=', $val[1])->firstOrFail('id')->id;
            		$product = new Product();
                    if(strlen($val[3]) < 5) {
                        return response(['message' => 'code is invalid', 'value' => null], 500);
                    }
                    $product->code = $val[3];
            		$product->name = $val[0];
          	  	    $product->serial = $val[2];
             		$product->category_id = categoryProduct::where('name', '=', $val[4])->firstOrFail('id')->id;

          	 	$product->is_own = ($val[5] == 1) ? $val[5] : (($val[6] == 1) ? $val[6] : null);
                    $user = User::where('token', '=', $request->_token)->firstOrFail();
             		$product->is_product = $val[7];
             		$product->user_creator = $user->id;
             		$product->store_id = $store_id;
					$product->imei = $val[8];
             		$product->save();
             		Store_ditail::create([
                 		"user_sender_id" => $user->id,
                 		"store_geter_id" => $store_id,
                 		"product_id" => Product::latest('id')->firstOrFail()->id,
			]);
			$latestProduct = $product->with('userCreator')->latest('id')->firstOrFail();
			Describe::create([
                'user_sender' => $user->name . " " . $user->lastname ,
                'store_geter' => $val[1],
		'status' => 1,
				'describe_type' => Product::class,
				'describe_id' => $latestProduct->id
			]);
         	}
         } else {
            $store_id = Store::where('name', '=', $request->store)->firstOrFail('id')->id;
            $user = User::where('token', '=', $request->_token)->firstOrFail();
            $product = new Product();
            $product->name = $request->name;
            $product->is_own = ($request->spend == true) ? 0 : (($request->is_own == true) ? 1 : null );
            $product->is_product = $request->is_product;
            $product->category_id = categoryProduct::where('name', '=', $request->category)->firstOrFail('id')->id;
            $product->code = $request->code;
            $product->serial = $request->serial;
            $product->user_creator = $user->id;
            $product->imei = $request->imei;
            $product->store_id = $store_id;
            $product->save();

            Store_ditail::create([
                "user_sender_id" => $user->id,
                "store_geter_id" => $store_id,
                "user_geter_id" => $user->id,
                "getProduct" => Carbon::now(),
                "product_id" => Product::latest('id')->firstOrFail('id')->id
            ]);

            $latestProduct = $product->with('userCreator')->latest('id')->firstOrFail();
            Describe::create([
                'user_sender' => $user->name . " " . $user->lastname,
                'store_geter' => $request->store,
                'describe_type' => Product::class,
		'describe_id' => $latestProduct->id,
		'status' => 1
            ]);
         }
         	return response()->json(['message' => 'success to insert product', 'value' => null]);
    }

    public function getProductInfo($token, $product_id)
    {
	    $product = Product::where('id', '=', $product_id)->firstOrFail();
	    if(!empty($product)) {
		    return response(['message' => 'this is your product', 'value' => $product]);
	    }
      return response(["message" => "not Found", "value" => null], 404);
    }

    public function productDitails(Request $request)
    {
        $user = User::with("storeUserRelation")->where("token",$request->_token)->firstOrFail();

        $store = Store::where('name', $request->store_name)->firstOrFail('id');
        $category = categoryProduct::with('products')->where('name', $request->code)->firstOrFail('id');
        $pr = [];
        foreach($user->storeUserRelation as $store_id){
            if ($store_id->store_id == $store->id) {
				$flag = 0;
				$lastCount = (isset($request->filter)) ? intval($request->filter) : 7;
				$firstCount = 0;
                foreach($category->products as $product){
					while($flag < 7) {

					$p_geter = Store_ditail::where('product_id', '=',$product->id)->where('store_geter_id', '=',$store_id->store_id)->where([['created_at', '<=', Carbon::now()->subDays($firstCount)->toDateTimeString()],['created_at', '>',Carbon::now()->subDays($lastCount)->toDateTimeString()]])->count();
						$p_sender = Store_ditail::where('product_id', '=',$product->id)->where('store_sender_id', '=',$store_id->store_id)->where([['created_at', '<=', Carbon::now()->subDays($firstCount)->toDateTimeString()],['created_at', '>',Carbon::now()->subDays($lastCount)->toDateTimeString()]])->count();
							$pr[] = ['store_sender' => $p_sender, 'store_geter' => $p_geter, 'latestTime' => Carbon::now()->subDays($lastCount)->toDateTimeString(), 'firstTime' => Carbon::now()->subDays($firstCount)->toDateTimeString()];

						if(isset($request->filter)){
							$lastCount += intval($request->filter);
							$firstCount += intval($request->filter);
						} else {
							$lastCount += 7;
							$firstCount += 7;
						}
						$flag += 1;
					}
                }
				return response(['message' => 'these are category ditails', 'value' => $pr], 200);

            }
        }


        return response(['message' => 'access denied to store', 'value' => null], 403);
    }
}
