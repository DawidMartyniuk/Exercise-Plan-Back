<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\TrainingSessions; // Use the correct model name if it exists
use App\Models\TrainingExercises;
use App\Models\TrainingSets;

class TrainingSesionsController extends Controller
{
    /**
     * Log the user ID and session data.
     */
    public function logSessionData()
    {
        $user = Auth::user();
        if ($user) {
            Log::info('User ID: ' . $user->id);
        } else {
            Log::warning('No user is logged in.');
        }
    }
    public function index()
    {
         $user = Auth::user();

           if (!$user) {
            return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
        }
        $exercises = TrainingSessions::where('user_id', $user->id)->get();

        $formattedExercises = $exercises->map(function ($exercise) {

            return [
                'id' => $exercise->id,
                'exercise_table_id' => $exercise->exercise_table_id,
                'exercise_table_name' => $exercise->exerciseTableName,
                'started_at' => $exercise->started_at,
                'duration' => $exercise->duration,
                'completed' => $exercise->completed,
                'total_weight' => $exercise->total_weight,
                'description' => $exercise->description,
                'image_base64' => $exercise->image_base64,
                'exercises' => $exercise->exercises->map(function ($ex) {
                    return [
                        'exercise_id' => $ex->exercise_id,
                        'notes' => $ex->notes,
                        'sets' => $ex->sets->map(function ($set) {
                            return [
                                'colStep' => $set->colStep,
                                'actual_kg' => $set->actual_kg,
                                'actual_reps' => $set->actual_reps,
                                'completed' => $set->completed,
                                'to_failure' => $set->to_failure,
                            ];
                        }),
                    ];
                }),
            ];
        });

        return response()->json(['message' => 'Training sessions retrieved successfully.', 'data' => $formattedExercises]);
    }


    
    public function store(Request $request)
    {
        Log::info('Received payload:', $request->all());

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
        }
        

       
        Log::info('Szukam exercise_table_id: ' . $request->exercise_table_id);
        $exerciseTable = \App\Models\ExerciseTable::with('rowsData.rows')->findOrFail($request->exercise_table_id);


        $this->logSessionData();
        
        $request->validate([
            'exercise_table_id' => 'required|exists:exercise_table,id',
            'exercise_table_name' => 'required|string|max:255',
            'started_at' => 'required|date',
            'duration' => 'nullable',
            'completed' => 'boolean',
            'total_weight' => 'nullable|numeric',
            'description' => 'nullable|string',
            'image_base64' => 'nullable|string',

            'exercises' => 'required|array',
            'exercises.*.exercise_id' => 'required|string',
            'exercises.*.notes' => 'nullable|string',

            'exercises.*.sets' => 'required|array',

            'exercises.*.sets.*.actual_kg' => 'nullable|numeric',
            'exercises.*.sets.*.actual_reps' => 'nullable|integer',
            'exercises.*.sets.*.completed' => 'boolean',
            'exercises.*.sets.*.to_failure' => 'boolean',

        ]);

        

        $savedPlannedExercises = [];

        $sesions = TrainingSessions::create([
            "user_id" => $user->id,
            "exercise_table_id" => $request->exercise_table_id,
            "exerciseTableName" => $exerciseTable->name, 
            "started_at" => $request->started_at,
            "duration" => $request->duration,
            "completed" => $request->completed,
            "total_weight" => $request->total_weight,
            "description" => $request->description,
            "image_base64" => $request->image_base64,
        ]);

        foreach( $request->exercises as $exerciseData) {
            $exercise = TrainingExercises::create([
                'training_session_id' => $sesions->id,
                'exercise_id' => $exerciseData['exercise_id'],
                'notes' => $exerciseData['notes'] ?? null,
            ]);
            foreach($exerciseData['sets'] as $setData){
                $set = TrainingSets::create([
                    'training_exercise_id' => $exercise->id,
                    'colStep' => $setData['colStep'],
                    'actual_kg' => $setData['actual_kg'] ?? null,
                    'actual_reps' => $setData['actual_reps'] ?? null,
                    'completed' => $setData['completed'] ?? false,
                    'to_failure' => $setData['to_failure'] ?? false,
                ]);
            }
        }
        $savedPlannedExercises[] = $sesions;

        return response()->json([
            'message' => 'Training session created successfully.',
            'planned_exercises' => $savedPlannedExercises,
        ], status: 200);
    }
    public function delete($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
        }

        $session = TrainingSessions::where('id', $id)
                                   ->where('user_id', $user->id)
                                   ->first();
        if (!$session) {
            return response()->json(['message' => 'Sesja treningowa nie została znaleziona.'], 404);
        }

        // Usuwanie powiązanych ćwiczeń i zestawów treningowych
        $session->exercises()->each(function ($exercise) {
            $exercise->sets()->delete();
            $exercise->delete();
        });

        // Usuwanie samej sesji
        $session->delete();

        return response()->json(['message' => 'Sesja treningowa została usunięta.'], 200);
    }
}
