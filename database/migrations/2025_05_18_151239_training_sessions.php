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
        if (!Schema::hasTable('training_sessions')) {
            Schema::create('training_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('exercise_table_id')->nullable();
                $table->timestamp('started_at')->nullable();
               $table->integer('duration')->nullable();
                $table->timestamp('ended_at')->nullable(); // zamiast tego ile czasu trwał trening
                $table->boolean('completed')->default(true);
                $table->double('total_weight')->nullable(); 
                $table->text('description')->nullable(); 
                $table->longText('image_base64')->nullable(); 
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('exercise_table_id')->references('id')->on('exercise_table')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropDatabaseIfExists('training_sessions');
    }
};
