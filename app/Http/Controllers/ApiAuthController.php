<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    public function register(Request $request){

        $request->validate([
            "name" => "required|min:3",
            "email" => "required|email|unique:users",
            "password" => "required|min:8|confirmed"
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);

        // if(Auth::attempt($request->only(['email','password']))){
        //    $token = Auth::user()->createToken("phone")->plainTextToken;
        //    return response()->json($token);
        // }

        return response()->json([
            "message" => "Registration Successful.",
            "success" => true
        ],200);

    }
    public function login(Request $request){
        $request->validate([
            "email" => "required",
            "password" => "required|min:8"
        ]);

        if(Auth::attempt($request->only(['email','password']))){
            $token = Auth::user()->createToken("phone")->plainTextToken;
            return response()->json([
                "message" => "Login Successful.",
                "success" => true,
                "token" => $token,
                "auth" => new UserResource(Auth::user())
            ]);
         }
         return response()->json([
            "message" => "User Not Found",
            "success" => false
        ],401);
    }

    public function logout(){
        Auth::user()->currentAccessToken()->delete();
        //return response()->json(["message" => "Logged out successfully"],204);
        return response()->json([
            "message" => "Logout Successful",
            "success" => true
        ]);
    }

    public function logoutAll(){
        Auth::user()->tokens()->delete();
        return response()->json(["message" => "Logged out of All Sessions successfully"],204);
    }

    public function logoutExcept(){
        $currentToken = Auth::user()->currentAccessToken();

        Auth::user()->tokens()->where('id', '!=', $currentToken->id)->delete();
        return response()->json(["message" => "Logged out of All Sessions successfully"],204);
    }

    public function tokens(){
        return Auth::user()->tokens;
    }
}
