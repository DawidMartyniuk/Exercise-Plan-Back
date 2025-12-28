<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExerciseTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\WeightConversion;

class ExerciseTableController extends Controller
{
    use WeightConversion;
    /**
     * Pobierz wszystkie dane exercise_table dla zalogowanego użytkownika.
     */
     public function index()
    {
        $user = Auth::user();

        $exercises = ExerciseTable::where('user_id', $user->id)->get();

        $formattedExercises = $exercises->map(function ($exercise) {
            return [
                'id' => $exercise->id,
                'exercise_table' => $exercise->exercise_table,
                'rows' => $exercise->rowsData->map(function ($row) {
                    return [
                        'exercise_number' => $row->exercise_number,
                        'exercise_name' => $row->exercise_name,
                        'notes' => $row->notes,
                        'rep_type' => $row->rep_type,
                        'data' => $row->rows->map(function ($dataRow) {

                               $convertedWeight = $this->convertWeight(
                                $dataRow->colKg, 
                                $dataRow->weight_unit ?? 'kg', 
                                $user->preferred_weight_unit ?? 'kg'
                            );
                            return [
                                'colStep' => $dataRow->colStep,
                                'colKg' => $convertedWeight,
                                'colRepMin' => $dataRow->colRepMin,
                                'colRepMax' => $dataRow->colRepMax,
                                'weight_unit' => $user->preferred_weight_unit ?? 'kg',
                                'original_weight_unit' => $dataRow->weight_unit ?? 'kg',
                                'rep_display' => $dataRow->rep_display, // "6" lub "5-7"
                                'is_range' => $dataRow->isRepRange(),
                            ];
                        }),
                    ];
                }),
            ];
        });

      
        Log::info('Zwracane dane:', $formattedExercises->toArray());

        return response()->json($formattedExercises);
    }



    public function store(Request $request)
    {
        Log::info('Received payload:', $request->all());
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
        }

        $request->validate([
            'exercises' => 'required|array',
            'exercises.*.exercise_table' => 'required|string',
            'exercises.*.rows' => 'required|array',
            'exercises.*.rows.*.exercise_number' => 'required|string',
            'exercises.*.rows.*.exercise_name' => 'required|string',
            'exercises.*.rows.*.notes' => 'nullable|string',
            'exercises.*.rows.*.rep_type' => 'required|in:single,range',
            'exercises.*.rows.*.data' => 'required|array',
            'exercises.*.rows.*.data.*.colStep' => 'required|integer',
            'exercises.*.rows.*.data.*.colKg' => 'required|integer',
            'exercises.*.rows.*.data.*.colRepMin' => 'required|integer',
            'exercises.*.rows.*.data.*.colRepMax' => 'nullable|integer', 
            'exercises.*.rows.*.data.*.weight_unit' => 'nullable|in:kg,lbs', 
        ]);

        $savedExercises = [];

        foreach ($request->exercises as $exerciseData) { 

            $exercise = ExerciseTable::create([
                'user_id' => $user->id,
                'exercise_table' => $exerciseData['exercise_table'],
            ]);


            foreach ($exerciseData['rows'] as $groupedRow) {
                $rowData = $exercise->rowsData()->create([
                    'exercise_id' => $exercise->id,
                    'exercise_number' => $groupedRow['exercise_number'],
                    'exercise_name' => $groupedRow['exercise_name'],
                    'notes' => $groupedRow['notes'] ??  null,
                    'rep_type' => $groupedRow['rep_type'],
                ]);

                foreach ($groupedRow['data'] as $row) {
                    $weightUnit = $row['weight_unit'] ?? $user->preferred_weight_unit ?? 'kg';


                 $rowData->rows()->create([
                    'colStep' => $row['colStep'],
                    'colKg' => $row['colKg'],
                    //'colRep' => $row['colRep'],
                    'colRepMin' => $row['colRepMin'],
                    'colRepMax' => $row['colRepMax'] ?? $row['colRepMin'],
                    'weight_unit' => $weightUnit,
                 ]);
                }
            }

            $savedExercises[] = $exercise->load('rowsData.rows');
        }

        return response()->json([
            'message' => 'Ćwiczenia zostały zapisane.',
            'exercises' => $savedExercises,
            'weight_unit_used' => $user->preferred_weight_unit ?? 'kg'
        ], 200);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $exercise = ExerciseTable::where('id', $id)->where('user_id', $user->id)->first();

        if (!$exercise) {
            return response()->json(['message' => 'Nie znaleziono ćwiczenia lub brak dostępu.'], 404);
        }

       
        
       
        $exercise->rowsData()->each(function($rowData) {
            $rowData->rows()->delete(); // Usuń exercise_rows dla każdego rowData
        });
        
        // 2. Usuń wszystkie exercise_rows_data
        $exercise->rowsData()->delete();
        
        // 3. Na końcu usuń główną tabelę exercise_table
        $exercise->delete();

        return response()->json(['message' => 'Cały plan został usunięty.'], 200);
    }

    /**
     * Zaktualizuj dane exercise_table dla zalogowanego użytkownika.
     */
    public function update(Request $request, $id)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
    }

    $exercise = ExerciseTable::where('id', $id)->where('user_id', $user->id)->first();

    if (!$exercise) {
        return response()->json(['message' => 'Nie znaleziono ćwiczenia lub brak dostępu.'], 404);
    }

    // Walidacja danych wejściowych
    $request->validate([
        'exercise_table' => 'required|string',
        'rows' => 'sometimes|array',
        'rows.*.exercise_number' => 'sometimes|string',
        'rows.*.exercise_name' => 'sometimes|string',
        'rows.*.notes' => 'sometimes|nullable|string',
        'rows.*.rep_type' => 'sometimes|in:single,range',
        'rows.*.data' => 'sometimes|array',
        'rows.*.data.*.colStep' => 'sometimes|integer',
        'rows.*.data.*.colKg' => 'sometimes|numeric',
        'rows.*.data.*.colRepMin' => 'sometimes|integer',
        'rows.*.data.*.colRepMax' => 'sometimes|nullable|integer',
        'rows.*.data.*.weight_unit' => 'sometimes|in:kg,lbs',
    ]);

    try {
        // Aktualizuj nazwę tabeli
        $exercise->update([
            'exercise_table' => $request->exercise_table,
        ]);

        // Jeśli przesłano nowe dane rows, zaktualizuj je
        if ($request->has('rows')) {
            // Usuń wszystkie stare dane
            $exercise->rowsData()->each(function($rowData) {
                $rowData->rows()->delete();
            });
            $exercise->rowsData()->delete();

            // Dodaj nowe dane
            foreach ($request->rows as $groupedRow) {
                $rowData = $exercise->rowsData()->create([
                    'exercise_id' => $exercise->id,
                    'exercise_number' => $groupedRow['exercise_number'],
                    'exercise_name' => $groupedRow['exercise_name'],
                    'notes' => $groupedRow['notes'] ?? null,
                    'rep_type' => $groupedRow['rep_type'] ?? 'single',
                ]);

                foreach ($groupedRow['data'] as $row) {
                    $weightUnit = $row['weight_unit'] ?? $user->preferred_weight_unit ?? 'kg';

                    $rowData->rows()->create([
                        'colStep' => $row['colStep'],
                        'colKg' => $row['colKg'],
                        'colRepMin' => $row['colRepMin'],
                        'colRepMax' => $row['colRepMax'] ?? $row['colRepMin'],
                        'weight_unit' => $weightUnit,
                    ]);
                }
            }
        }

        
        $updatedExercise = $exercise->load('rowsData.rows');

        
        $formattedExercise = [
            'id' => $updatedExercise->id,
            'exercise_table' => $updatedExercise->exercise_table,
            'rows' => $updatedExercise->rowsData->map(function ($row) use ($user) {
                return [
                    'exercise_number' => $row->exercise_number,
                    'exercise_name' => $row->exercise_name,
                    'notes' => $row->notes,
                    'rep_type' => $row->rep_type,
                    'data' => $row->rows->map(function ($dataRow) use ($user) {
                        $convertedWeight = $this->convertWeight(
                            $dataRow->colKg, 
                            $dataRow->weight_unit ?? 'kg', 
                            $user->preferred_weight_unit ?? 'kg'
                        );
                        
                        return [
                            'colStep' => $dataRow->colStep,
                            'colKg' => $convertedWeight,
                            'colRepMin' => $dataRow->colRepMin,
                            'colRepMax' => $dataRow->colRepMax,
                            'weight_unit' => $user->preferred_weight_unit ?? 'kg',
                            'original_weight_unit' => $dataRow->weight_unit ?? 'kg',
                            'rep_display' => $dataRow->rep_display ?? (string)$dataRow->colRepMin,
                            'is_range' => method_exists($dataRow, 'isRepRange') ? $dataRow->isRepRange() : false,
                        ];
                    }),
                ];
            }),
        ];

        return response()->json([
            'message' => 'Plan treningowy został zaktualizowany.',
            'exercise' => $formattedExercise,
            'weight_unit_used' => $user->preferred_weight_unit ?? 'kg'
        ]);

    } catch (\Exception $e) {
        Log::error("Błąd podczas aktualizacji planu: " . $e->getMessage());
        return response()->json(['message' => 'Błąd podczas aktualizacji planu.'], 500);
    }
}
}
