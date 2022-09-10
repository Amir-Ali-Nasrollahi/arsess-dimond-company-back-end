<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class authUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $_token = (isset($request->_token)) ? $request->_token : $request->route("token");
        $token = User::where("token", "=", $_token)->firstOrFail();
        if (empty($token)) {
            return response()->json(['message'=>'invalid token', 'value'=>null],401);
        }
        if ($token->status == false) return response(["message"=>"access dinaid", "value"=>null],403);
        return $next($request);
    }
}
