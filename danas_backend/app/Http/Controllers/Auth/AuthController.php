<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $username_int = $request->username;
        $pass_int = md5($request->password);
        $user = User::where(['login' => $username_int, 'password' => $pass_int, 'aktif' => true])->first();
        
        if(is_null($user)){
            return response()->json(['status' => false, 'message' => ['error' => ['Username or Password Not Found']]], 400); 
        }

        try {
            if (!$token = $this->guard('api')->login( $user )) {
                return response()->json(['status' => false, 'message' => ['error' => ['Username or Password are invalid OR user not active']]], 400); 
            }
        } catch(JWTException $e) {
            return response()->json(['status' => false, 'message' => ['error' => ['Cannot create Token']]], 400);
        }
        
        return $this->respondWhToken($token, $user);   
    }

    public function logout() {
        $this->guard()->logout();
        return response()->json(['success' => true,'message'=>'Sucessfully Logout'], 200);
    }

    public function user(Request $request) {
        $user = User::where(['login' => $request->username, 'password' => $request->password, 'aktif' => true])->first(); 
        return response()->json($this->guard()->login($user));
    }
    
    public function refresh(Request $request)
    {
        return $this->respondWhToken($this->guard()->refresh(), $request);
    }

    public function guard() {
        return Auth::guard('api');
    }

    protected function respondWhToken($token, $user) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'user' => $user   
        ]);
    }
}
