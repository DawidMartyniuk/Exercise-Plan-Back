<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Dodaj rep_type tylko do exercise_rows_data jeśli nie istnieje
        if (!Schema::hasColumn('exercise_rows_data', 'rep_type')) {
            Schema::table('exercise_rows_data', function (Blueprint $table) {
                $table->enum('rep_type', ['single', 'range'])->default('single')->after('notes');
            });
        }
        
        // NIE ROBIMY NIC z exercise_rows - już ma wszystkie potrzebne kolumny!
        // Tabela exercise_rows już ma: colRepMin, colRepMax, weight_unit
    }

    /**
     * Reverse the migrations.
     */
    // public function down(): void
    // {
    //     if (Schema::hasColumn('exercise_rows_data', 'rep_type')) {
    //         Schema::table('exercise_rows_data', function (Blueprint $table) {
    //             $table->dropColumn('rep_type');
    //         });
    //     }
    // }
};
