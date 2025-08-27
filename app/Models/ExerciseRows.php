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
        'weight_unit',
    ];
     protected $appends = ['converted_col_kg', 'display_unit'];
    public function exerciseData(): BelongsTo
    {
        return $this->belongsTo(ExerciseRowsData::class, 'row_data_id');
    }

     public function getConvertedColKgAttribute()
    {
        // Pobierz preferencje użytkownika przez relacje
        $user = $this->exerciseData?->exercise?->user ?? null;
        
        if ($user && $user->preferred_weight_unit !== $this->weight_unit) {
            return $this->convertWeight(
                $this->colKg, 
                $this->weight_unit, 
                $user->preferred_weight_unit
            );
        }
        return $this->colKg;
    }

    public function getDisplayUnitAttribute()
    {
        $user = $this->exerciseData?->exercise?->user ?? null;
        return $user ? $user->preferred_weight_unit : $this->weight_unit;
    }
}