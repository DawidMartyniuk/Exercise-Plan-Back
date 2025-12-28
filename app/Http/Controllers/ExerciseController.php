<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exercise;
use Illuminate\Support\Facades\Auth;

class ExerciseController extends Controller
{
    public function index(){
        $user = Auth::user();

        $exercises = Exercise::where('user_id', $user->id)->get();

        //formatedExericse 

        return $exercises;
    }
  public function create(Request $request){
    $request->validate([
        'external_id' => 'required|string|unique:exercises,external_id',
        //'user_id' => 'required|integer',
        'name' => 'required|string',
        'gif' => 'required|file|mimes:gif,jpg,jpeg,png',
        'target_muscles' => 'required|array',
        'body_parts' => 'required|array',
        'equipments' => 'required|array',
        'secondary_muscles' => 'required|array',
        'instructions' => 'required|array',
    ]);
    $patch = $request->file('gif')->store('gifs', 'public');
     // Utwórz URL do pliku
    $gifUrl = asset('storage/' . $patch);

    $user = Auth::user();

    $exercise = Exercise::create([
        'user_id' => $user->id,
        'external_id' => $request->external_id,
        'name' => $request->name,
        'gif_url' => $gifUrl,
        'target_muscles' => $request->target_muscles,
        'body_parts' => $request->body_parts,
        'equipments' => $request->equipments,
        'secondary_muscles' => $request->secondary_muscles,
        'instructions' => $request->instructions,
    ]);

    return response()->json($exercise, 201);
}

}

