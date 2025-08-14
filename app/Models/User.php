<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\TrainingSessions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject; // Upewnij się, że ten namespace jest dodany
use App\Notifications\CustomResetPassword;

class User extends Authenticatable implements JWTSubject // Upewnij się, że implementujesz JWTSubject
{

    /**
 * @method string createToken(string $name, array $abilities = [])
 */
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens ;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'description',
        'weight',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function exercises(){

        return $this->hasMany(ExerciseTable::class);
    }
    public function trainingSessions()
    {
        return $this->hasMany(TrainingSessions::class);
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
//     public function sendPasswordResetNotification($token)
// {
//     $this->notify(new CustomResetPassword($token));
// }


    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
