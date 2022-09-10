<?php

namespace App\Http\Controllers;

use App\Models\Bag;
use App\Models\Own;
use App\Models\Product;
use App\Models\Store;
use App\Models\Store_ditail;
use App\Models\storeUserRelation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function validation(Request $request)
    {
        $user = User::with(["bag","own","storeUserRelation","user_geter","user_sender"])->where("token", "=", $request->_token)->firstOrFail();
        foreach($user->storeUserRelation as $store_id) {
            $store[] = Store::where('id', '=', $store_id->store_id)->firstOrFail();
        }
	$user->store = $store;
        return response(["message"=>"token is valid", "value"=>$user],200);;

    }

    public function store(Request $request)
    {
        $user = new User();
        $store = new Store();
        $own = new Own();

        if(count($user->latest("id")->get()) == 0 && count($store->latest("id")->get()) == 0) {

            $user->status = 1;
            $user->name = $request->firstname;
            $user->lastname = $request->lastname;
            $user->phone = $request->phone;
            $user->code = $request->code;
            $user->password = Hash::make($request->password);
            $user->token = $request->_token;
            $user->save();

            $store->name = "موقت";
            $store->save();
            $id = 1;
            storeUserRelation::create([
                'store_id' => 1,
                'user_id' => 1
            ]);
            $own->user_id = 1;
            $own->addProducts = 1;
            $own->authProducts = 1;
            $own->authUser = 1;
            $own->makeStore = 1;
            $own->sendProducts = 1;
            $own->loginProduct = 1;
            $own->isAdmin = 1;
            $own->save();
        } else {
            $user->status = 0;
            $user->name = $request->firstname;
            $user->lastname = $request->lastname;
            $user->phone = $request->phone;
            $user->code = $request->code;
            $user->password = Hash::make($request->password);
            $user->token = $request->_token;
            $user->save();
            $id = User::where("code", "=", $request->code)->firstOrFail("id")->id;

            $own->user_id = $id;
            $own->addProducts = 0;
            $own->authProducts = 0;
            $own->authUser = 0;
            $own->makeStore = 0;
            $own->sendProducts = 0;
            $own->save();
        }

        $bag = new Bag();
        $bag->user_id = $id;
        $bag->save();
        return response()->json(['message'=>'successful adding user', 'value'=>null],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function check(Request $request) {
        $user = User::where('phone','=',$request->phone)->get(['password','status']);
		if ($user == Hash::check($request->password, $user[0]->password)) {
            return response()->json(["message"=>"welcome to the dashboard","value"=>['status'=>$user[0]->status]], 200);
		}
		return response()->json(['message'=>'user not found','value'=>null],404);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = User::where("id", "=", $request->id)->firstOrFail();
        $user->status = 1;
		$user->save();
        $user->update(["status" => 1]);
        Own::where("user_id", "=", $user->id)->update([
            "addProducts" => $request->addProduct,
            "authProducts" => $request->authProduct,
            "makeStore" => $request->makeStore,
            "authUser" => $request->authUser,
            "sendProducts" => $request->sendProduct,
	    'loginProduct' => $request->loginProduct,
	    'isAdmin' => $request->isAdmin
        ]);
        foreach($request->stores_name as $store_name){
		$store_id = Store::where('name', '=', $store_name)->firstOrFail('id')->id;
            storeUserRelation::create([
                'store_id' => $store_id,
                'user_id' => $user->id
            ]);
        }
        // return $request;
        return response()->json(["message" => "changed status", "value" => null], 200);
    }


    /**
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $users=User::with(['storeUserRelation', 'own'])->get();
	foreach($users as $user){
		$storeValue = [];
		foreach($user->storeUserRelation as $store) {
			$storeValue[] = Store::find($store->store_id);
		}
		$user->storeUserRelation = $storeValue;
	}
	    return response()->json(["message"=>"these are all users", "value"=>$users]);
    }

    public function destroy($token, $id)
    {
        $user = User::find($id);
        Store_ditail::where('user_geter_id', '=', $id)->update([
            'user_geter_id' => 1
        ]);
        Store_ditail::where('user_sender_id', '=', $id)->update([
            'user_sender_id' => 1
        ]);
        Product::where('user_creator', '=', $id)->update([
            'user_creator' => 1
        ]);
        $user->delete();
        return response(["message"=>"deleted user successfully", "value" => null]);
    }
    public function changePassword(Request $request) {
	    //return "TEST";
    	$user = User::where("token", "=", $request->_token);
        if(Hash::check($request->latestPassword, $user->firstOrFail()->password)) {
            $user->update(['password' => Hash::make($request->newPassword)]);
            return response(['message' => 'changed password successfully', 'value' => null]);
        }
        return response(['message' => 'your latest password is incorrect !', 'value' => null], 400);
    }
    public function showOneUser($token, $id) {
	    $user = User::with(['own', 'storeUserRelation'])->findOrFail($id);
	    foreach($user->storeUserRelation as $user_relation) {
	    	$stores[] = Store::findOrFail($user_relation->store_id);
	    }
	    $store = Store::all();
	    return response(['message' => 'this is a user' , 'value' => ['user' => $user , 'stores' => $stores, 'store' => $store]]);
    }

    public function editUser(Request $request) {
	    $own = Own::where('user_id', '=', $request->id)->firstOrFail();
    		$own->authUser = $request->authUser;
		$own->makeStore = $request->makeStore;
    		$own->addProducts = $request->addProduct;
		$own->loginProduct = $request->loginProduct;
		$own->authProducts = $request->authProduct;
		$own->sendProducts = $request->sendProduct;
		$own->isAdmin = $request->isAdmin;
		$own->save();

		$relationStore = storeUserRelation::where('user_id', '=', $request->id)->get();
		foreach($relationStore as $relation){
			$relation->delete();
		}

       	foreach($request->stores as $store_name){
			$store_id = Store::where('name', '=', $store_name)->firstOrFail('id')->id;
            		storeUserRelation::create([
                		'store_id' => $store_id,
               		 	'user_id' => $request->id
            		]);
		 }
		return response(['message' => 'change password successfully', 'value' => null]);
    }
}
