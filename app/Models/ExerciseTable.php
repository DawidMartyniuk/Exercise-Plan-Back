<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ExerciseRow;

class ExerciseTable extends Model
{
    protected $table = 'exercise_table';

    protected $fillable = [
        'user_id',
        'exercise_table',
    ];

    // // Exercise należy do jednego usera
    // public function user(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }
      public function rows(): HasMany
    {
        return $this->hasMany(ExerciseRow::class, 'exercise_id');
    }
}
