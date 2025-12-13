<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercise_rows_data', function (Blueprint $table) {
            // Sprawdź i usuń foreign key tylko jeśli istnieje
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'exercise_rows_data' 
                AND COLUMN_NAME = 'exercise_id' 
                AND CONSTRAINT_NAME != 'PRIMARY'
            ");

            foreach ($foreignKeys as $fk) {
                $table->dropForeign([$fk->CONSTRAINT_NAME]);
            }
        });

        // Teraz zmień typ kolumny
        Schema::table('exercise_rows_data', function (Blueprint $table) {
            $table->unsignedBigInteger('exercise_id')->change();
        });

        // Dodaj nowy foreign key
        Schema::table('exercise_rows_data', function (Blueprint $table) {
            $table->foreign('exercise_id')->references('id')->on('exercise_table')->onDelete('cascade');
        });
    }

    // public function down(): void
    // {
    //     Schema::table('exercise_rows_data', function (Blueprint $table) {
    //         $table->dropForeign(['exercise_id']);
    //         $table->string('exercise_id')->change();
    //     });
    // }
};