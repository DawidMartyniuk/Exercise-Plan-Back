<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\TrainingSession;
use App\Models\ExerciseLibrary;

class TrainingExercises extends Model
{
    protected $table = 'training_exercises';

    protected $fillable = [
        'training_session_id',
        'exercise_id', 
        'notes',
        
    ];
     public function trainingSession()
    {
        return $this->belongsTo(TrainingSessions::class);
    }

    public function exerciseLibrary()
    {
        return $this->belongsTo(ExerciseLibrary::class);
    }

    public function trainingSets()
    {
        return $this->hasMany(TrainingSets::class);
    }
    public function sets()
    {
        return $this->hasMany(TrainingSets::class, 'training_exercise_id');
    }

}