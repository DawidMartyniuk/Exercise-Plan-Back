<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\WeightConversion;
//TODO: LOGI LOGOWANIA i REJESTRACJI

class TrainingSets extends Model
{
    use HasFactory, WeightConversion;

    protected $table = 'training_sets';

    protected $fillable = [
        'training_exercise_id',
        'colStep',
        'actual_kg',
        'actual_reps',
        'weight_type', // Dodaj to
        'completed',
        'to_failure',
    ];

    protected $appends = ['converted_actual_kg', 'display_unit'];

    public function trainingExercise()
    {
        return $this->belongsTo(TrainingExercises::class, 'training_exercise_id');
    }

    // Accessor do automatycznej konwersji
    public function getConvertedActualKgAttribute()
    {
        $user = $this->trainingExercise->trainingSession->user ?? null;
        if ($user && $user->preferred_weight_unit !== $this->weight_type) {
            return $this->convertWeight(
                $this->actual_kg, 
                $this->weight_type, 
                $user->preferred_weight_unit
            );
        }
        return $this->actual_kg;
    }

    public function getDisplayUnitAttribute()
    {
        $user = $this->trainingExercise->trainingSession->user ?? null;
        return $user ? $user->preferred_weight_unit : $this->weight_type;
    }
}
