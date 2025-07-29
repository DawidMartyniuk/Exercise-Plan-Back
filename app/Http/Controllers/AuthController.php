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
            'avatar' => 'sometimes|string', // opcjonalny avatar
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ];

        // Dodaj avatar tylko jeśli został przesłany
        if ($request->has('avatar')) {
            $userData['avatar'] = $request->avatar;
        }

        $user = User::create($userData);

        return response()->json($user, 201); 
    }
public function updateProfile(Request $request)
{
    $request->validate([
        'name' => 'sometimes|string|max:255',
        'avatar' => 'sometimes|string', // base64 string
    ]);

    $authUser = Auth::user();
    
    if (!$authUser) {
        return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
    }

    // Ensure we get the Eloquent User model instance
    $user = \App\Models\User::find($authUser->id);

    $updateData = [];
    
    if ($request->has('name')) {
        $updateData['name'] = $request->name;
    }
    
    if ($request->has('avatar')) {
        $updateData['avatar'] = $request->avatar;
    }

    $user->update($updateData);

    return response()->json([
        'message' => 'Profil został zaktualizowany.',
        'user' => $user
    ]);
}
public function getProfile()
{
    $user = Auth::user();
    
    if (!$user) {
        return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
    }

    return response()->json([
        'user' => $user
    ]);
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
