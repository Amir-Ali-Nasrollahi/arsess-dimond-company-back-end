<?php

namespace App\Http\Controllers;

use App\Models\Bag;
use App\Models\Ditail;
use App\Models\User;
use App\Models\Describe;
use App\Models\Product;
use App\Models\Store_ditail;
use Illuminate\Http\Request;
use Carbon\Carbon;
class BagController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		$user = User::with('bag')->where("token", "=", $request->_token)->firstOrFail();
		$ditail = Ditail::where([['bag_id' , '=', $user->bag->id], ['product_id', '=',$request->product_id]])->firstOrFail();
		$ditail->delete();
	    Store_ditail::create([
	    	'store_geter_id' => 1,
		    'user_sender_id' =>$user->id,
		    'product_id' => $request->product_id,
            'user_geter_id' => 1,
            'getProduct' => Carbon::now()
	    ]);
		$trashedProduct = Product::onlyTrashed()->findOrFail($request->product_id);
		$trashedProduct->update([
			'store_id' => 1
        ]);
        $trashedProduct->restore();
        Describe::create([
			'user_sender' => $user->name,
			'store_geter' => 'موقت',
			'status' => 1,
			'describe_id' => $request->product_id,
			'describe_type' => Product::class
		]);
		return response(["message" => "SUCCESS", "value"=>null]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	    $user_name = explode(' ', $request->user);
        $user = User::with(["bag"])->where([["name", "=", $user_name[0]], ['lastname', '=', $user_name[1]]])->firstOrFail();
        $ditail = new Ditail();
        $ditail->bag_id = $user->bag->id;
        $ditail->product_id = $request->product_id;
        $ditail->status = 1;
        $store_ditail = Store_ditail::where('product_id', '=' , $request->product_id)->get();
        $ditail->save();

		Product::findOrFail($request->product_id)->delete();

 		$user_sender = User::where("token", "=", $request->_token)->firstOrFail();
		Describe::create([
        	"user_sender" => $user_sender->name . " " . $user_sender->lastname,
        	'user_geter' => $request->user,
			'describe_id' => $request->product_id,
			'describe_type' => Product::class
		]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($token)
    {
	    $user = User::where("token", "=", $token)->firstOrFail("id");

	    $val = Bag::with(["user", "ditails"])->where("user_id", "=", $user->id)->firstOrFail();
        if(count($val->ditails) !== 0) {
            foreach($val->ditails as $value){
                $data[] = ["product"=>Product::withTrashed()->findOrFail($value->product_id), "count"=>$value->count, "date" => $value->created_at];
            }
	        return response()->json(["message"=>"your products", "value"=>$data]);
        }
        return response()->json(["message"=>"your bag is empty", "value"=>null]);
    }
}
