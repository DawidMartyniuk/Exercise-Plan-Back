<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

public function up(): void
{
    // 1. Usuń FK jeśli istnieją (bezpiecznie)
    try {
        Schema::table('exercise_rows', function (Blueprint $table) {
            $table->dropForeign('exercise_rows_row_data_id_foreign');
        });
    } catch (\Throwable $e) {}

    try {
        Schema::table('exercise_rows_data', function (Blueprint $table) {
            $table->dropForeign('exercise_rows_data_exercise_id_foreign');
        });
    } catch (\Throwable $e) {}

    // 2. Zamień tabele miejscami
    Schema::rename('exercise_rows', 'tmp_swap_exercise_rows');
    Schema::rename('exercise_rows_data', 'exercise_rows');
    Schema::rename('tmp_swap_exercise_rows', 'exercise_rows_data');

    // 3. DODAJ POPRAWNE KLUCZE (już po zamianie!)

    // exercise_rows → dane ćwiczenia
    if (Schema::hasColumn('exercise_rows', 'exercise_id')) {
        Schema::table('exercise_rows', function (Blueprint $table) {
            $table->foreign('exercise_id')
                ->references('id')
                ->on('exercise_table')
                ->onDelete('cascade');
        });
    }

    // exercise_rows_data → serie
    if (Schema::hasColumn('exercise_rows_data', 'row_data_id')) {
        Schema::table('exercise_rows_data', function (Blueprint $table) {
            $table->foreign('row_data_id')
                ->references('id')
                ->on('exercise_rows')
                ->onDelete('cascade');
        });
    }
}
};
