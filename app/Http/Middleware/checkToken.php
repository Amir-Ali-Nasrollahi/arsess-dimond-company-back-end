<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class checkToken
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
        if (!isset($request->_token) && $request->_token != hash('sha256', $request->phone)) {
            return response()->json(['message'=>'invalid token', 'value'=>null],401);
        } 
        return $next($request);

    }
}
