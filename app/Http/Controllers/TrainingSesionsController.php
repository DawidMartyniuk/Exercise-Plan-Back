<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\TrainingSessions;
use App\Models\TrainingExercises;
use App\Models\TrainingSets;
use App\Traits\WeightConversion;

class TrainingSesionsController extends Controller
{
    use WeightConversion;

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
        
        $exercises = TrainingSessions::where('user_id', $user->id)
            ->with(['exercises.sets'])
            ->get();

        $formattedExercises = $exercises->map(function ($exercise) use ($user) {
            // Konwertuj wagi zgodnie z preferencjami użytkownika
            $totalWeightConverted = $this->convertWeight(
                $exercise->total_weight, 
                $exercise->weight_type ?? 'kg', 
                $user->preferred_weight_unit ?? 'kg'
            );

            return [
                'id' => $exercise->id,
                'exercise_table_id' => $exercise->exercise_table_id,
               
                'started_at' => $exercise->started_at,
                'duration' => $exercise->duration,
             
                'total_weight' => $totalWeightConverted,
                'weight_unit' => $user->preferred_weight_unit ?? 'kg',
                'original_weight_unit' => $exercise->weight_type ?? 'kg',
                'description' => $exercise->description,
                'image_base64' => $exercise->image_base64,
                'exercises' => $exercise->exercises->map(function ($ex) use ($user) {
                    return [
                        'exercise_id' => $ex->exercise_id,
                        'notes' => $ex->notes,
                        'sets' => $ex->sets->map(function ($set) use ($user) {
                            $actualKgConverted = $this->convertWeight(
                                $set->actual_kg, 
                                $set->weight_type ?? 'kg', 
                                $user->preferred_weight_unit ?? 'kg'
                            );

                            return [
                                'colStep' => $set->colStep,
                                'actual_kg' => $actualKgConverted,
                                'actual_reps' => $set->actual_reps,
                                'weight_unit' => $user->preferred_weight_unit ?? 'kg',
                                'original_weight_unit' => $set->weight_type ?? 'kg',
                                'completed' => $set->completed,
                                'to_failure' => $set->to_failure,
                            ];
                        }),
                    ];
                }),
            ];
        });

        return response()->json([
            'message' => 'Training sessions retrieved successfully.', 
            'data' => $formattedExercises,
            'user_preferred_unit' => $user->preferred_weight_unit ?? 'kg'
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Received payload:', $request->all());

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
        }

        $request->validate([
            'exercise_table_id' => 'required|exists:exercise_table,id',
          //  'exercise_table_name' => 'required|string|max:255',
            'started_at' => 'required|date',
            'duration' => 'nullable',
          //  'completed' => 'boolean',
            'total_weight' => 'nullable|numeric',
            'weight_unit' => 'nullable|in:kg,lbs', 
            'description' => 'nullable|string',
            'image_base64' => 'nullable|string',
            'exercises' => 'required|array',
            'exercises.*.exercise_id' => 'required|string',
            'exercises.*.notes' => 'nullable|string',
            'exercises.*.sets' => 'required|array',
            'exercises.*.sets.*.actual_kg' => 'nullable|numeric',
            'exercises.*.sets.*.actual_reps' => 'nullable|integer',
            'exercises.*.sets.*.weight_unit' => 'nullable|in:kg,lbs', 
            'exercises.*.sets.*.completed' => 'boolean',
            'exercises.*.sets.*.to_failure' => 'boolean',
        ]);

        $exerciseTable = \App\Models\ExerciseTable::with('rowsData.rows')
            ->findOrFail($request->exercise_table_id);

        // Użyj jednostki z requestu lub domyślnej preferencji użytkownika
        $weightUnit = $request->weight_unit ?? $user->preferred_weight_unit ?? 'kg';

        $sesions = TrainingSessions::create([
            "user_id" => $user->id,
            "exercise_table_id" => $request->exercise_table_id,
           // "exerciseTableName" => $exerciseTable->name, 
            "started_at" => $request->started_at,
            "duration" => $request->duration,
           // "completed" => $request->completed,
            "total_weight" => $request->total_weight,
            "weight_type" => $weightUnit, // Dodaj jednostkę
            "description" => $request->description,
            "image_base64" => $request->image_base64,
        ]);

        foreach($request->exercises as $exerciseData) {
            $exercise = TrainingExercises::create([
                'training_session_id' => $sesions->id,
                'exercise_id' => $exerciseData['exercise_id'],
                'notes' => $exerciseData['notes'] ?? null,
            ]);
            
            foreach($exerciseData['sets'] as $setData){
                $setWeightUnit = $setData['weight_unit'] ?? $weightUnit;
                
                $set = TrainingSets::create([
                    'training_exercise_id' => $exercise->id,
                    'colStep' => $setData['colStep'],
                    'actual_kg' => $setData['actual_kg'] ?? null,
                    'actual_reps' => $setData['actual_reps'] ?? null,
                    'weight_type' => $setWeightUnit, // Dodaj jednostkę
                    'completed' => $setData['completed'] ?? false,
                    'to_failure' => $setData['to_failure'] ?? false,
                ]);
            }
        }

        return response()->json([
            'message' => 'Training session created successfully.',
            'session' => $sesions,
            'weight_unit_used' => $weightUnit,
        ], 200);
    }
    public function update(Request $request, $id){

        $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
    }

    $sesions = TrainingSessions::where('id', $id)->where('user_id', $user->id)->first();

    if (!$sesions) {
        return response()->json(['message' => 'Sesja treningowa nie została znaleziona.'], 404);
    }

    // POPRAWKA: Usuń podwójną walidację
    $request->validate([
        'exercise_table_id' => 'required|exists:exercise_table,id',
        'started_at' => 'required|date',
        'duration' => 'nullable|integer',
        'total_weight' => 'nullable|numeric',
        'weight_unit' => 'nullable|in:kg,lbs', 
        'description' => 'nullable|string',
        'image_base64' => 'nullable|string',
        'exercises' => 'required|array',
        'exercises.*.exercise_id' => 'required|string',
        'exercises.*.notes' => 'nullable|string',
        'exercises.*.sets' => 'required|array',
        'exercises.*.sets.*.colStep' => 'required|integer',
        'exercises.*.sets.*.actual_kg' => 'nullable|numeric',
        'exercises.*.sets.*.actual_reps' => 'nullable|integer',
        'exercises.*.sets.*.weight_unit' => 'nullable|in:kg,lbs', 
        'exercises.*.sets.*.completed' => 'nullable|boolean',
        'exercises.*.sets.*.to_failure' => 'nullable|boolean',
    ]);

    try{
        // POPRAWKA: Usuń pola które nie istnieją w modelu
        $sesions->update([
            'exercise_table_id' => $request->exercise_table_id,
            'started_at' => $request->started_at,
            'duration' => $request->duration,
            'total_weight' => $request->total_weight,
            'weight_unit' => $request->weight_unit ?? 'kg',
            'description' => $request->description,
            'image_base64' => $request->image_base64,
        ]);

        // Usuń stare ćwiczenia i zestawy
        if ($request->has('exercises')) {
            $sesions->exercises()->each(function ($exercise) {
                $exercise->sets()->delete();
                $exercise->delete();
            });
        }

        // Dodaj nowe ćwiczenia
        foreach($request->exercises as $exerciseData) {
            $exercise = TrainingExercises::create([
                'training_session_id' => $sesions->id,
                'exercise_id' => $exerciseData['exercise_id'],
                'notes' => $exerciseData['notes'] ?? null,
            ]);
            
            foreach($exerciseData['sets'] as $setData){
                $setWeightUnit = $setData['weight_unit'] ?? ($request->weight_unit ?? 'kg');
                
                TrainingSets::create([
                    'training_exercise_id' => $exercise->id,
                    'colStep' => $setData['colStep'],
                    'actual_kg' => $setData['actual_kg'] ?? null,
                    'actual_reps' => $setData['actual_reps'] ?? null,
                    'weight_type' => $setWeightUnit, 
                    'completed' => $setData['completed'] ?? false,
                    'to_failure' => $setData['to_failure'] ?? false,
                ]);
            }
        }

        $updateSessions = $sesions->load(['exercises.sets']);
        
        $formatedSession = [
            'id' => $updateSessions->id,
            'exercise_table_id' => $updateSessions->exercise_table_id,
            'started_at' => $updateSessions->started_at,
            'duration' => $updateSessions->duration,
            'total_weight' => $updateSessions->total_weight,
            'weight_unit' => $updateSessions->weight_unit,
            'description' => $updateSessions->description,
            'image_base64' => $updateSessions->image_base64,
            'exercises' => $updateSessions->exercises->map(function ($ex) {
                return [
                    'exercise_id' => $ex->exercise_id,
                    'notes' => $ex->notes,
                    'sets' => $ex->sets->map(function ($set) {
                        return [
                            'colStep' => $set->colStep,
                            'actual_kg' => $set->actual_kg,
                            'actual_reps' => $set->actual_reps,
                            'weight_unit' => $set->weight_type,
                            'completed' => $set->completed,
                            'to_failure' => $set->to_failure,
                        ];
                    }),
                ];
            }),  
        ];

        return response()->json([
            'message' => 'Sesja treningowa została zaktualizowana pomyślnie.',
            'session' => $formatedSession,
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Wystąpił błąd podczas aktualizacji sesji treningowej.',
            'error' => $e->getMessage()
        ], 500);
    }
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
