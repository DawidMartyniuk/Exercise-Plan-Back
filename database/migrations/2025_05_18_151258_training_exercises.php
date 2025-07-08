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
        if (!Schema::hasTable('training_exercises')) {
            Schema::create('training_exercises', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('training_session_id');
                $table->unsignedBigInteger('exercise_id'); // z jakiego ćwiczenia
                $table->string('notes')->nullable(); // z planu
                $table->timestamps();

                $table->foreign('training_session_id')->references('id')->on('training_sessions')->onDelete('cascade');
                $table->foreign('exercise_library_id')->references('id')->on('exercise_library')->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_exercises');
    }
};
