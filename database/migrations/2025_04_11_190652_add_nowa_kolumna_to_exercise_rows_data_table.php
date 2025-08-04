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
        if (!Schema::hasTable('exercise_rows_data')) {
            Schema::create('exercise_rows_data', function (Blueprint $table) {
                $table->engine = 'InnoDB'; 
                $table->id();
                $table->string('exercise_id');
                $table->integer('exercise_number');
                $table->string('exercise_name');
                $table->string('notes')->nullable();
                $table->timestamps();
    
                $table->foreign('exercise_id')->references('id')->on('exercise_table')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
        {
            Schema::dropIfExists('exercise_rows_data');
        }
};
