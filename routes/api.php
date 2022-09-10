<?php

use App\Http\Controllers\BagController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OwnController;
use Illuminate\Http\Request;
use App\Http\Controllers\CategoryProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('authUser')->controller(BagController::class)->group(function () {
    Route::post('/bag', 'store');
    Route::get('/bag/{token}', 'show');
    Route::post('/backProduct', "create");
});


Route::controller(UserController::class)->group(function () {
    Route::patch("/users/{token}","update")->middleware("authUser");
    Route::get("/users/{token}", "show")->middleware("authUser");
    Route::delete("/users/{token}/{id}", "destroy")->middleware("authUser");
    Route::post('/user', "store")->middleware(["checkToken"]);
    Route::get('/changeProfile/{token}/{id}', 'showOneUser')->middleware('authUser');
    Route::post('/changePassword', 'changePassword')->middleware('authUser');
    Route::patch('/editUser/{token}', 'editUser')->middleware('authUser');
    // when request check the validation
    Route::post('/validate',"validation")->middleware("authUser");
    // check user Status login
    Route::post('/checkLogin',"check")->middleware("checkToken");
});

Route::middleware('authUser')->controller(StoreController::class)->group(function () {
    Route::post('/store', 'store');
    Route::post("/search", "searchProduct");
    Route::get('/getAllStores/{token}','showAllStores');
    Route::post("/sendtootherstore", "create");
    Route::post("/showStore", "showStore");
    Route::get('/store/{token}', 'show');
    Route::patch('/store/{token}', 'update');
    Route::delete('/deleteStore/{token}/{id}',"deleteStore");
    Route::delete("/deletestoreditails/{token}/{id}","destroy");
});

Route::get("/checkOwn/{token}", [OwnController::class, 'show'])->middleware("authUser");

Route::middleware('authUser')->controller(\App\Http\Controllers\CategoryProductController::class)->group(function () {
    Route::post("/category", 'store');
    Route::get("/chartCategory/{token}", 'showCategoryForChart');
    Route::get("/category/{token}", 'show');
    Route::delete('/deleteCategory/{token}/{category_id}', 'delete');
});

Route::middleware('authUser')->controller(ProductController::class)->group(function () {
    Route::post('/acceptAll','acceptAll');
    Route::post('/product', 'store');
    Route::get("/getproductinfo/{token}/{product_id}", "getProductInfo");
    Route::post("/productDitails" , "productDitails");
});

