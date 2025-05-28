<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseRowsData extends Model
{
    protected $table = 'exercise_rows_data';

    protected $fillable = [
        'exercise_id',
        'exercise_number',
        'exercise_name',
        'notes',
    ];
   
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(ExerciseTable::class, 'exercise_id');
    }

    // Relacja do pojedynczych serii (rows)
    public function rows(): HasMany
    {
        return $this->hasMany(ExerciseRows::class, 'row_data_id');
    }
}

