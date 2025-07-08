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
        'started_at',
        'ended_at',
        'completed',
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
    public function trainingExercise()
    {
            return $this->hasMany(ExerciseTable::class, 'exercise_table_id');
    }

      
 }