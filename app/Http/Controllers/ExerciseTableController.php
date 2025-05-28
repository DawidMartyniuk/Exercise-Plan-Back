<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExerciseTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExerciseTableController extends Controller
{
    /**
     * Pobierz wszystkie dane exercise_table dla zalogowanego użytkownika.
     */public function index()
{
    $user = Auth::user();
        
    $exercises = ExerciseTable::where('user_id', $user->id)->get();

    $formattedExercises = $exercises->map(function ($exercise) {
        return [
            'id' => $exercise->id,
            'exercise_table' => $exercise->exercise_table,
            'rows' => $exercise->rowsData->map(function($row){
                return [
                    'exercise_number' => $row->exercise_number,
                    'exercise_name' => $row->exercise_name,
                    'notes' => $row->notes,
                    'data' => $row->rows->map(function($dataRow){
                        return [
                            'colStep' => $dataRow->colStep,
                            'colKg' => $dataRow->colKg,
                            'colRep' => $dataRow->colRep,
                        ];
                    }),
                ];
            }),
        ];
    });

    // 👇 KLUCZOWA ZMIANA
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
                'exercises.*.rows.*.exercise_number' => 'required|integer',
                'exercises.*.rows.*.exercise_name' => 'required|string',
                'exercises.*.rows.*.notes' => 'nullable|string',
                'exercises.*.rows.*.data' => 'required|array',
                'exercises.*.rows.*.data.*.colStep' => 'required|integer',
                'exercises.*.rows.*.data.*.colKg' => 'required|integer',
                'exercises.*.rows.*.data.*.colRep' => 'required|integer',
            ]);

            $savedExercises = [];

            foreach ($request->exercises as $exerciseData) {
           
                $exercise = ExerciseTable::create([
                    'user_id' => $user->id,
                    'exercise_table' => $exerciseData['exercise_table'],
                ]);

           
                foreach($exerciseData['rows'] as $groupedRow){
                    $rowData = $exercise->rowsData()->create([
                        'exercise_id' => $exercise->id,
                        'exercise_number' => $groupedRow['exercise_number'],
                        'exercise_name' => $groupedRow['exercise_name'],
                        'notes' => $groupedRow['notes'] ??  null,
                    ]);

                
                    foreach ($groupedRow['data'] as $row) {
                        $rowData->rows()->create([
                            'colStep'=> $row['colStep'],
                            'colKg' => $row['colKg'],
                            'colRep' => $row['colRep'],
                        ]);
                    }
                }

                $savedExercises[] = $exercise->load('rowsData.rows');
            }

            return response()->json([
                'message' => 'Ćwiczenia zostały zapisane.',
                'exercises' => $savedExercises,
            ], 200);
        }
    

   
    public function destroy($id)
    {
        $user = Auth::user();

        // Znajdź rekord i upewnij się, że należy do zalogowanego użytkownika
        $exercise = ExerciseTable::where('id', $id)->where('user_id', $user->id)->first();

        if (!$exercise) {
            return response()->json(['message' => 'Nie znaleziono ćwiczenia lub brak dostępu.'], 404);
        }

        $exercise->delete();

        return response()->json(['message' => 'Ćwiczenie zostało usunięte.']);
    }

    /**
     * Zaktualizuj dane exercise_table dla zalogowanego użytkownika.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Znajdź rekord i upewnij się, że należy do zalogowanego użytkownika
        $exercise = ExerciseTable::where('id', $id)->where('user_id', $user->id)->first();

        if (!$exercise) {
            return response()->json(['message' => 'Nie znaleziono ćwiczenia lub brak dostępu.'], 404);
        }

        // Walidacja danych wejściowych
        $request->validate([
            'exercise_table' => 'required|string',
        ]);

        // Aktualizacja danych
        $exercise->update([
            'exercise_table' => $request->exercise_name,
        ]);

        return response()->json(['message' => 'Dane zostały zaktualizowane.', 'exercise' => $exercise]);
    }
}