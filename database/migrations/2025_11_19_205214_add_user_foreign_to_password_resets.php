<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_resets', function (Blueprint $table) {
            // Dodaj kolumnę user_id tylko jeśli nie istnieje
            if (!Schema::hasColumn('password_resets', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('email');
            }
        });

        // Sprawdź czy foreign key już istnieje
        $foreignKeyExists = DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'password_resets' 
            AND COLUMN_NAME = 'user_id' 
            AND CONSTRAINT_NAME != 'PRIMARY'
        ");

        // Dodaj foreign key tylko jeśli nie istnieje
        if ($foreignKeyExists[0]->count == 0) {
            Schema::table('password_resets', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    // public function down(): void
    // {
    //     Schema::table('password_resets', function (Blueprint $table) {
    //         // Sprawdź i usuń foreign key jeśli istnieje
    //         $foreignKeys = DB::select("
    //             SELECT CONSTRAINT_NAME 
    //             FROM information_schema.KEY_COLUMN_USAGE 
    //             WHERE TABLE_SCHEMA = DATABASE() 
    //             AND TABLE_NAME = 'password_resets' 
    //             AND COLUMN_NAME = 'user_id' 
    //             AND CONSTRAINT_NAME != 'PRIMARY'
    //         ");

    //         foreach ($foreignKeys as $fk) {
    //             $table->dropForeign([$fk->CONSTRAINT_NAME]);
    //         }

    //         // Usuń kolumnę jeśli istnieje
    //         if (Schema::hasColumn('password_resets', 'user_id')) {
    //             $table->dropColumn('user_id');
    //         }
    //     });
    // }
};