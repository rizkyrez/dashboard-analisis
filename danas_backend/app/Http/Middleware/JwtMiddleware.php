<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if (!$user = JWTAuth::parseToken()->login()) {
                return response()->json(['success' => false,'message'=>'User Tidak ditemukan'],404);
            }else{
                if($user['aktif']==0){
                    return response()->json(['success' => false,'message'=>'User Tidak Aktif'],401);
                }
            }
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['success' => false,'message'=>'Token is Invalid'],400);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['success' => false,'message'=>'Token is Expired'],408);
            }else{
                return response()->json(['success' => false,'message'=>'Authorization Token not found'],401);
            }
        }
        return $next($request);
    }
}
