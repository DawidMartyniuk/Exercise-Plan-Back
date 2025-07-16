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
            // Usuń ended_at
            if (Schema::hasColumn('training_sessions', 'ended_at')) {
                $table->dropColumn('ended_at');
            }
            // Dodaj duration (np. w minutach)
            $table->integer('duration')->nullable()->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
      */
    // public function down(): void
    // {
    //     Schema::table('training_sessions', function (Blueprint $table) {
    //         $table->dropColumn('duration');
    //         $table->timestamp('ended_at')->nullable()->after('started_at');
    //     });
    // }
};