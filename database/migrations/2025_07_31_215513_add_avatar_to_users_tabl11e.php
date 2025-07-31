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
        Schema::table('users', function (Blueprint $table) {
            // Sprawdź, czy kolumny już istnieją
            if (!Schema::hasColumn('users', 'description')) {
                $table->string('description')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'weight')) {
                $table->integer('weight')->nullable()->after('description'); // zmień int na integer
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->longText('avatar')->nullable()->after('password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    // public function down(): void
    // {
    //     Schema::table('users', function (Blueprint $table) {
    //         $table->dropColumn(['description', 'weight', 'avatar']);
    //     });
    // }
};
