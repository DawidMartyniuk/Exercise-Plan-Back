<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    /**
     * Pobierz profil zalogowanego użytkownika
     */
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

    /**
     * Zaktualizuj profil użytkownika (nazwa, opis, waga)
     */
    public function updateProfile(Request $request)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $userId, // Wyklucz aktualnego użytkownika
            'description' => 'sometimes|string|max:500',
            'weight' => 'sometimes|integer|min:1|max:500',
            'preferred_weight_unit' => 'sometimes|in:kg,lbs',
            'avatar' => 'sometimes|string',
        ]);

        // Pobierz użytkownika bezpośrednio z bazy
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Użytkownik nie został znaleziony.'], 404);
        }

        $updateData = [];

        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }

        if ($request->has('email')) {
            $updateData['email'] = $request->email;
        }

        if ($request->has('description')) {
            $updateData['description'] = $request->description;
        }

        if ($request->has('weight')) {
            $updateData['weight'] = $request->weight;
        }
        if ($request->has('preferred_weight_unit')) {
            $updateData['preferred_weight_unit'] = $request->preferred_weight_unit;
        }
        
        if ($request->has('avatar')) {
            $updateData['avatar'] = $request->avatar;
        }

        $user->update($updateData);

        return response()->json([
            'message' => 'Profil został zaktualizowany.',
            'user' => $user->fresh()
        ]);
    }

    /**
     * Zaktualizuj avatar użytkownika
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|string', // base64 string
        ]);

        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
        }

        // Pobierz użytkownika bezpośrednio z bazy
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Użytkownik nie został znaleziony.'], 404);
        }

        if (!$user) {
            return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
        }

        $user->update([
            'avatar' => $request->avatar,
        ]);

        return response()->json([
            'message' => 'Avatar został zaktualizowany.',
            'user' => $user
        ]);
    }

    /**
     * Usuń avatar użytkownika
     */
    public function deleteAvatar()
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
        }

        // Pobierz użytkownika bezpośrednio z bazy
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Użytkownik nie został znaleziony.'], 404);
        }

        if (!$user) {
            return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
        }

        $user->update([
            'avatar' => null,
        ]);

        return response()->json([
            'message' => 'Avatar został usunięty.',
            'user' => $user
        ]);
    }

    public function updateWeightPreferences(Request $request)
    {
        $request->validate([
            'preferred_weight_unit' => 'required|in:kg,lbs',
        ]);

        $userId = Auth::id();

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
        }

        $user->update([
            'preferred_weight_unit' => $request->preferred_weight_unit,
        ]);

        return response()->json([
            'message' => 'Preferencje jednostek zostały zaktualizowane.',
            'user' => $user,
            'preferred_weight_unit' => $user->preferred_weight_unit
        ]);
    }
}
