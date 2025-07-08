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
        if (!Schema::hasTable('training_sets')) {
            Schema::create('training_sets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('training_exercise_id');
                $table->integer('colStep');
                $table->integer('planned_kg')->nullable();
                $table->integer('planned_reps')->nullable();
                $table->integer('actual_kg')->nullable();
                $table->integer('actual_reps')->nullable();
                $table->boolean('completed')->default(false);
                $table->boolean('to_failure')->default(false); 
                $table->timestamps();

                $table->foreign('training_exercise_id')->references('id')->on('training_exercises')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_sets');
    }
};
