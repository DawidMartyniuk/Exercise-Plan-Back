<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercise_rows_data', function (Blueprint $table) {
            // Zmień typ kolumny na string bez usuwania foreign key
            $table->string('exercise_id')->change();
        });
    }

};