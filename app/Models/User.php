<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'username',
        'email',
        'phone_number',
        'blood_type',
        'sex',
        'birth_date',
        'hospital',
        'photo',
        'email_verified_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hospital' => 'array',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'datetime',
        ];
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function accidents()
    {
        return $this->belongsToMany(Accident::class, 'accident_user');
    }
    public function getBirthDateAttribute($value)
    {
        // Utilisez Carbon pour formater la date
        return Carbon::parse($value)->format('d-m-Y'); // Format par exemple : '01/01/1980'
    }
}
