<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('exercise_rows_data')) {
            Schema::create('exercise_rows_data', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exercise_id');
                $table->string('exercise_name');
                $table->string('notes')->nullable();
                $table->timestamps();
    
                $table->foreign('exercise_id')->references('id')->on('exercise_table')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_rows_data');
    }
};

