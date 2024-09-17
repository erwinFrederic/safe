<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emergency extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'longitude',
        'latitude',
        'phone_number_1',
        'phone_number_2',
        'phone_number_3',
    ];

    public function accidents()
    {
        return $this->hasMany(Accident::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
