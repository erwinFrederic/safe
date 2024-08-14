<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'relation',
        'profesional_situation',
        'phone_number_1',
        'phone_number_2',
        'phone_number_3',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}