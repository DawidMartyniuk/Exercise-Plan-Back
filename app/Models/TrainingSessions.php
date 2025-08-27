<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSessions extends Model
{
      protected $table = 'training_sessions';

      protected $fillable = [
        'user_id',
        'exercise_table_id',
        'exercise_table_name',
        'started_at',
        'duration', 
        'completed',
        'weight_unit',//
        'total_weight',
        'description',
        'image_base64',
      ];
      public function user()
      {
            return $this->belongsTo(User::class);
      }
    public function exerciseTable()
    {
            return $this->belongsTo(ExerciseTable::class);
    }
    public function getCronvertedTotalWeightAttribute()
    {
         $user = $this->user;
        if ($user && $user->preferred_weight_unit !== $this->weight_type) {
            return $this->convertWeight(
                $this->total_weight, 
                $this->weight_type, 
                $user->preferred_weight_unit
            );
        }
        return $this->total_weight;
    }
      public function getDisplayUnitAttribute()
    {
        $user = $this->user;
        return $user ? $user->preferred_weight_unit : $this->weight_type;
    }

    public function exercises()
    {
        return $this->hasMany(TrainingExercises::class,'training_session_id' );
    }

      
 }