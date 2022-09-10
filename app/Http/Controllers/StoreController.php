<?php

namespace App\Http\Controllers;

use App\Models\Own;
use App\Models\Product;
use App\Models\Store;
use App\Models\Store_ditail;
use App\Models\Describe;
use App\Models\storeUserRelation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAllStores()
    {
	    $store = Store::all();
	    return response()->json(['message'=>'these are All stores','value'=>$store]);
    }

    /**
     * Show the form for creating a new resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if(isset($request->groupAdd)) {
			$uploadFile = $request->file('file');
			$fileName = time().$uploadFile->getClientOriginalName();
			Storage::disk('local')->putFileAs('public/',$uploadFile, $fileName);
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		    $value = $reader->load('../public/storage/' . $fileName)->getSheet(0)->toArray();
			Storage::disk('local')->delete('public/'.$fileName);
 			$user = User::with('storeUserRelation')->where("token", "=", $request->_token)->firstOrFail();
			foreach($value as $val){
				$storeSender = Store::where('name', '=', $val[1])->firstOrFail();
				foreach($user->storeUserRelation as $store_id) {
					if($store_id->store_id == $storeSender->id){
                        $store_geter = Store::where("name", "=", $val[2])->firstOrFail(['id', 'name']);
                        $product_id =  Product::where("code", "=", $val[0])->firstOrFail('id')->id;
                        $p = Product::findOrFail($product_id);
                        $p->update([
                            'store_id' => $store_geter->id,
                        ]);
                        $p->delete();
						Store_ditail::create([
							"store_sender_id" => $storeSender->id,
							"store_geter_id" => $store_geter->id,
							"user_sender_id" => $user->id,
							"product_id" => $product_id
						]);
                        Describe::create([
                            'user_sender' => $user->name . " " . $user->lastname,
                            'store_geter' => $store_geter->name,
                            'store_sender' => $storeSender->name,
                            'describe_id' => $product_id,
                            'describe_type' => Product::class
                        ]);
					}
				}
			}

		}else{
            $store_geter_id = Store::where('name', '=', $request->store_geter_name)->firstOrFail('id')->id;
            $p = Product::findOrFail($request->product_id);
            $p->update([
                'store_id' => $store_geter_id,
            ]);
            $p->delete();
        	$store_ditail = new Store_ditail();
        	$store_ditail->store_sender_id = $request->store_sender_id;
        	$store_ditail->store_geter_id = $store_geter_id;
			$userid = User::where("token", "=", $request->_token)->firstOrFail();

	    	$store_ditail->user_sender_id = $userid->id;
       		$store_ditail->product_id = $request->product_id;
        	$store_ditail->save();
			Describe::create([
                'user_sender' => $userid->name . " " . $userid->lastname,
                'store_geter' => $request->store_geter_name,
                'store_sender' => Store::findOrFail($request->store_sender_id)->name,
				'describe_id' => $request->product_id,
				'describe_type' => Product::class
			]);
		}
        return response(["message"=>"send data successfully", "value" => null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $store = new Store();
        $store->name = $request->name;
        $store->save();
        $userOwn = Own::where('isAdmin', '=', 1)->get();
        foreach($userOwn as $id) {
            storeUserRelation::create([
                'user_id' => $id->user_id,
                'store_id' => $store->latest()->firstOrFail('id')->id,
            ]);
        }
        return response(["message" => "success to create store", "value" => null]);
    }

    public function showStore(Request $request)
    {
        $user = User::with('own')->where("token", "=", $request->_token)->firstOrFail();
        if($user->own->isAdmin == true){
            $product[] = Product::withoutTrashed()->with('describe')->paginate(20);
            return response(["message" => "these are your products", "value" => $product]);
        }

        foreach($request->id as $store_id) {
            $product[] = Product::withoutTrashed()->with('describe')->where('store_id', $store_id)->paginate(20);
        }
        return response(["message" => "these are your products", "value" => $product]);

    }
	/*
	 * @return \Illuminate\Http\Response
	 *
	 * */
    public function show($token) {
        $user = User::with(['storeUserRelation'])->where("token", "=", $token)->firstOrFail();

        foreach($user->storeUserRelation as $store_id) {
            $product[] = Product::onlyTrashed()->with('describe')->where('store_id', $store_id->store_id)->get();
        }
	if(count($product[0]) > 0){
        	return response(['message' => 'this is te all requests' , 'value' => $product]);
	}
	return response(['message' => 'you havent any request', 'value' => null],404);
    }

    public function searchProduct(Request $request)
    {
        $user = User::with('own')->where("token", "=", $request->_token)->firstOrFail();
        if($user->own->isAdmin == true){
            $product[] = Product::with('describe')->where("code", $request->code)->firstOrFail();
        }
        return response(["message" => "these are your products", "value" => $product]);

        foreach($request->id as $store_id) {
            $product[] = Product::with('describe')->where([["code", '=',$request->code], ['store_id', '=', $store_id]], 'and')->first();
        }
        return response(["message" => "these are your products", "value" => $product]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$token)
    {
		$user = User::where('token', '=', $token)->firstOrFail();
        Product::onlyTrashed()->find($request->id)->restore();
	    $store_ditail = Store_ditail::where('product_id', '=', $request->id)->latest();
        $store_ditail->update(['user_geter_id' => $user->id, 'getProduct' => \Carbon\Carbon::now()->toDateTimeString()]);
		Describe::where("describe_id", "=", $request->id)->latest()->firstOrFail()->update([
		    'user_geter' => $user->name . " " . $user->lastname,
            'status' => 1
        ]);
	    return response(['message'=>'auth successfully', 'value'=>null]);
    }


	public function deleteStore($token, $id) {
		$store = Store::findOrFail($id);
		$store->delete();
		return response(["message" => "delete is success", 'value' => null]);
	}
    /**
     * Remove the specified resource from storage.
     *create
     * @return \Illuminate\Http\Response
     */
    public function destroy($token, $id)
    {
	    $p = Product::onlyTrashed()->find($id);
	    $p->update([
	    	'store_id' => Store::where('name', '=', Describe::where('describe_id' , '=', $id)->latest()->firstOrFail('store_sender')->store_sender)->firstOrFail('id')->id
	    ]);
	    $p->restore();
	    $store_ditail = Store_ditail::where('product_id', '=', $id)->latest()->firstOrFail();
	    $store_ditail->delete();
	    $user = User::where("token", "=", $token)->firstOrFail();
        Describe::where("describe_id", "=", $store_ditail->product_id)->latest()->firstOrFail()->update([
		    'user_geter' => $user->name . " " . $user->lastname,
            'status' => 0,
		]);
        return response(['message'=>'deleted successfully', 'value'=>null]);
    }
}
