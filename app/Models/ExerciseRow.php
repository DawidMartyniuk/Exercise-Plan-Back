<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseRow extends Model
{
    protected $table = 'exercise_rows';

    protected $fillable = [
        'exercise_id',
        'exercise_name',
        'notes',
        'colStep',
        'colKg',
        'colRep',
    ];

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(ExerciseTable::class, 'exercise_id');
    }
}