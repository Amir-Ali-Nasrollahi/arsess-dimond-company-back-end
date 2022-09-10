<?php

namespace App\Http\Controllers;

use App\Models\Own;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class OwnController extends Controller
{
    public function show($token)
    {
	    $own = User::with("own")->where("token", "=", $token)->firstOrFail();
	    return response()->json(["message"=>"your Owns", "value"=>$own->own]);
    }

}
