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
                    return response()->json(['success' => false,'message'=>'User Tidak Aktif']);
                }
            }
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['success' => false,'message'=>'Token is Invalid']);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['success' => false,'message'=>'Token is Expired']);
            }else{
                return response()->json(['success' => false,'message'=>'Authorization Token not found']);
            }
        }
        return $next($request);
    }
}
