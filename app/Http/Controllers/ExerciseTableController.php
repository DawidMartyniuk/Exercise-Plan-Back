<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExerciseTable;
use Illuminate\Support\Facades\Auth;

class ExerciseTableController extends Controller
{
    /**
     * Pobierz wszystkie dane exercise_table dla zalogowanego użytkownika.
     */
    public function index()
    {
        $user = Auth::user();

        // Pobierz dane z tabeli exercise_table dla zalogowanego użytkownika
        $exercises = ExerciseTable::where('user_id', $user->id)->get();

        return response()->json($exercises);
    }

    /**
     * Zapisz nowe dane exercise_table dla zalogowanego użytkownika.
     */
    public function store(Request $request)
        {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
            }

            // Walidacja danych wejściowych
            $request->validate([
                'exercises' => 'required|array',
                'exercises.*.exercise_table' => 'required|string',
                'exercises.*.rows' => 'required|array',
                'exercises.*.rows.*.exercise_name' => 'required|string',
                'exercises.*.rows.*.notes' => 'nullable|string',
                'exercises.*.rows.*.colStep' => 'required|integer',
                'exercises.*.rows.*.colKg' => 'required|integer',
                'exercises.*.rows.*.colRep' => 'required|integer',
            ]);

            $savedExercises = [];

            foreach ($request->exercises as $exerciseData) {
                $exercise = ExerciseTable::create([
                    'user_id' => $user->id,
                    'exercise_table' => $exerciseData['exercise_table'],
                ]);

                foreach ($exerciseData['rows'] as $row) {
                    $exercise->rows()->create($row);
                }

                $savedExercises[] = $exercise->load('rows');
            }

            return response()->json([
                'message' => 'Ćwiczenia zostały zapisane.',
                'exercises' => $savedExercises,
            ], 201);
        }
    

    /**
     * Usuń dane exercise_table dla zalogowanego użytkownika.
     */
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