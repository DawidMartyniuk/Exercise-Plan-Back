<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       // if (!Schema::hasTable('exercise_rows')) {
            Schema::create('exercise_rows', function (Blueprint $table) {
                $table->engine = 'InnoDB'; 
                $table->id();
                $table->unsignedBigInteger('row_data_id'); 
                $table->integer('colStep');
                $table->integer('colKg');
                $table->integer('colRep');
                $table->enum('weight_unit', ['kg', 'lbs'])->default('kg')->after('colRep');
                $table->timestamps();
    
                $table->foreign('row_data_id')->references('id')->on('exercise_rows_data')->onDelete('cascade');
            });
      //  }
    }
//

    public function down(): void
    {
        Schema::dropIfExists('exercise_rows');
    }
};