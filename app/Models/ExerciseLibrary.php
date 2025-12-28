<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseLibrary extends Model
{
    protected $table = 'exercise_library';

    protected $fillable = [
        'name',
        'body_part',
        'equipment',
        'gif_url',
        'description',
    ];
    public function trainingExercise()
    {
        return $this->hasMany(ExerciseTable::class, 'exercise_id');
    } 
}
