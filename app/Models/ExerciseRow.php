<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExerciseRow extends Model
{
    protected $table = 'exercise_rows';

    protected $fillable = [
        'row_data_id',
        'colStep',
        'colKg',
        'colRep',
    ];

    public function rows(): HasMany
    {
        return $this->hasMany(ExerciseRow::class, 'row_data_id');
    }
}