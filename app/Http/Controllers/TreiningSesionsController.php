<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\TrainingSessions; // Use the correct model name if it exists
use App\Models\TrainingExercises;
use App\Models\TrainingSets;

class TreiningSesionsController extends Controller
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
        $this->logSessionData();
        // Your logic to retrieve training sessions
        return response()->json(['message' => 'Training sessions retrieved successfully.']);
    }
    public function store(Request $request)
    {
        Log::info('Received payload:', $request->all());

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
        }
        $exerciseTable = \App\Models\ExerciseTable::with('rowsData.rows')->findOrFail($request->exercise_table_id);


        $this->logSessionData();
        // Validate and store the training session data
        $request->validate([
            'exercise_table_id' => 'required|exists:exercise_table,id',
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date',
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

        $session = TrainingSessions::create($request->all());

        // Assuming you have a method to handle exercises and sets

        $savedPlannedExercises = [];
        // foreach ($request->input('exercises', []) as $exerciseData) {
        //     $exercise = TrainingExercises::create([
        //         'training_session_id' => $session->id,
        //         'exercise_id' => $exerciseData['exercise_id'],
        //         'notes' => $exerciseData['notes'] ?? null,
        //     ]);

        //     foreach ($exerciseData['sets'] as $setData) {
        //         $set = TrainingSets::create([
        //             'training_exercise_id' => $exercise->id,
        //             'colStep' => $setData['colStep'],
        //             'planned_kg' => $setData['planned_kg'] ?? null,
        //             'planned_reps' => $setData['planned_reps'] ?? null,
        //             'actual_kg' => $setData['actual_kg'] ?? null,
        //             'actual_reps' => $setData['actual_reps'] ?? null,
        //             'completed' => $setData['completed'] ?? false,
        //             'to_failure' => $setData['to_failure'] ?? false,
        //         ]);
        //     }
        //     $savedPlannedExercises[] = $exercise;
       // }
        //foreach($request-> sesions as trainingSessions  )
        
            // 'exercises.*.sets' => 'required|array',

            // 'exercises.*.sets.*.actual_kg' => 'nullable|numeric',
            // 'exercises.*.sets.*.actual_reps' => 'nullable|integer',
            // 'exercises.*.sets.*.completed' => 'boolean',
            // 'exercises.*.sets.*.to_failure' => 'boolean',


        $sesions = TrainingSessions::create([
            "user_id" => $user->id,
            "exercise_table_id" => $request->exercise_table_id,
            "started_at" => $request->started_at,
            "ended_at" => $request->ended_at,
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
}
