<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request) {

        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);
        $username_int = $request->username;
        $pass_int = md5($request->password);
        $user = User::where(['login' => $username_int, 'password' => $pass_int, 'aktif' => true])->first();   
        // $credentials = ['login' => $usernamev, 'password' => $passwordv];
        try {
            if (!$token = $this->guard()->login( $user )) {
                return response()->json(['status' => 'error', 'message' => 'Username or Password are invalid OR user not active']); 
            }
         
        } catch(JWTException $e) {
            return response()->json(['status' => 'error', 'message' => 'Cannot create Token']);
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
