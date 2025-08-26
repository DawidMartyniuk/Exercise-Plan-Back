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
        Schema::table('training_sets', function (Blueprint $table) {
           $table->enum('weight_type', ['kg', 'lbs'])->default('kg  '); 
        });
         Schema::table('training_sessions', function (Blueprint $table) {
           $table->enum('weight_type', ['kg', 'lbs'])->default('kg  '); 
        });
       Schema::table('users', function (Blueprint $table) {
            $table->enum('preferred_weight_unit', ['kg', 'lbs'])->default('kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    // public function down(): void
    // {
    //     Schema::table('training_sets2', function (Blueprint $table) {
    //         //
    //     });
    // }
};
