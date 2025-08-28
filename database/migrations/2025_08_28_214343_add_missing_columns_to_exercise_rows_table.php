<?php
// filepath: database/migrations/YYYY_MM_DD_HHMMSS_add_missing_columns_to_exercise_rows_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercise_rows', function (Blueprint $table) {
            if (!Schema::hasColumn('exercise_rows', 'colRepMax')) {
                $table->integer('colRepMax')->nullable()->after('colRepMin');
            }
            
            if (!Schema::hasColumn('exercise_rows', 'weight_unit')) {
                $table->enum('weight_unit', ['kg', 'lbs'])->default('kg')->after('colRepMax');
            }
        });
    }

    // public function down(): void
    // {
    //     Schema::table('exercise_rows', function (Blueprint $table) {
    //         if (Schema::hasColumn('exercise_rows', 'colRepMax')) {
    //             $table->dropColumn('colRepMax');
    //         }
            
    //         if (Schema::hasColumn('exercise_rows', 'weight_unit')) {
    //             $table->dropColumn('weight_unit');
    //         }
    //     });
    // }
};