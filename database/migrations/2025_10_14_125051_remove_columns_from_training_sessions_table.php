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
        Schema::table('training_sessions', function (Blueprint $table) {
            // Sprawdź i usuń tylko istniejące kolumny
            if (Schema::hasColumn('training_sessions', 'exercise_table_name')) {
                $table->dropColumn('exercise_table_name');
            }
            if (Schema::hasColumn('training_sessions', 'completed')) {
                $table->dropColumn('completed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('training_sessions', function (Blueprint $table) {
        //     $table->string('exercise_table_name')->nullable();
        //     $table->boolean('completed')->default(true);
        // });
    }
};