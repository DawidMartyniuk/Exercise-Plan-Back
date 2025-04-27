<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseTable extends Model
{
    protected $table = 'exercise_table';

    protected $fillable = [
        'user_id',
        'exercise_table',
    ];

    // Exercise należy do jednego usera
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
