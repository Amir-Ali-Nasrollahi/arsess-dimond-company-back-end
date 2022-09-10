<?php

namespace App\Http\Controllers;

use App\Models\categoryProduct;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class CategoryProductController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	    categoryProduct::create([
	    	'name' => $request->name
	    ]);
	    return response(['message' => 'created successfuly', 'value' => null]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\categoryProduct  $categoryProduct
     * @return \Illuminate\Http\Response
     */
    public function show($token)
    {
	    return response()->json(['message'=>'these are all categories', 'value' => categoryProduct::all()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCategoryForChart($token)
    {
        $user = User::with(['own', 'storeUserRelation'])->where("token", "=", $token)->firstOrFail();
        if($user->own->isAdmin == true){
            $categories = categoryProduct::with('products')->get();
            foreach($categories as $category) {
                $name[] = $category->name;
                $count[] = count($category->products);
            }
        }

        return response(["message" => "these are your products", "value" => ['names' => $name, 'count' => $count]]);

        foreach($user->storeUserRelation as $store_id) {
            $stores[] = Store::with(['products', 'products.category'])->findOrFail($store_id->store_id);
        }
        $cat = [];
        foreach($stores as $store) {
            foreach($store->products as $products) {
                if(!array_key_exists($products->category->name, $cat)) {
                    $cat[$products->category->name] = array();
                }
                array_push($cat[$products->category->name],$products);
            }
        }
        $name = [];
        $count = [];
        foreach($cat as $key => $value){
            $name[] = $key;
            $count[] = count($value);
        }
        return response(["message" => "these are your products", "value" => ['names' => $name, 'count' => $count]]);

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\categoryProduct  $categoryProduct
     * @return \Illuminate\Http\Response
     */
    public function delete($token, $category_id)
    {
        categoryProduct::destroy($category_id);
	return response(['message' => 'deleted successfuly', 'value' => null]);
    }
}
