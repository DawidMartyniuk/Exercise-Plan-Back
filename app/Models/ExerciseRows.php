<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExerciseRows extends Model
{
    protected $table = 'exercise_rows';

    protected $fillable = [
        'row_data_id',
        'colStep',
        'colKg',
        'colRep',
    ];
    public function exerciseData(): BelongsTo
    {
        return $this->belongsTo(ExerciseRowsData::class, 'row_data_id');
    }
}