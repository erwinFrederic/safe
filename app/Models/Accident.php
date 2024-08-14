<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accident extends Model
{
    use HasFactory;
    protected $fillable = [
        'vehicle_id',
        'emergency_id',
        'longitude',
        'latitude',
        'report',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'accident_user');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function emergency()
    {
        return $this->belongsTo(Emergency::class);
    }
}