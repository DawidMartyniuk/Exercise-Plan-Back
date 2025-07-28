<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingSets extends Model
{
    protected $table = 'training_sets';

    protected $fillable = [
        'training_exercise_id',
        'colStep',
        'actual_kg',
        'actual_reps',
        'completed',
        'to_failure',
    ];

    public function trainingExercise()
    {
        return $this->belongsTo(TrainingExercises::class);
    }

}
