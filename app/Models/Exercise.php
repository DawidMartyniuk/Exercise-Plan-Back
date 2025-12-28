<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
     protected $table = 'exercises';

    protected $fillable = [
        'external_id',
        'user_id',
        'name',
        'gif_url',
        'target_muscles',
        'body_parts',
        'equipments',
        'secondary_muscles',
        'instructions',
    ];
    protected $casts = [
    'target_muscles' => 'array',
    'body_parts' => 'array',
    'equipments' => 'array',
    'secondary_muscles' => 'array',
    'instructions' => 'array',
];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
