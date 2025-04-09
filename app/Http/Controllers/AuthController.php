<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json($user, 201); 
    }
    public function login(Request $request){

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
    
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Błędne dane logowania'], 401);
        }
    
        $user = Auth::user(); 
    
      
        try {
            $token = JWTAuth::fromUser($user);
        } catch (Exception $e) {
            return response()->json(['message' => 'Nie udało się wygenerować tokenu'], 500);
        }
    
        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);

    }
    public function logout(Request $request){

        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Wylogowano']);

    }

}
