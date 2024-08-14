<?php

namespace Database\Seeders;

use App\Models\Accident;
use App\Models\Emergency;
use App\Models\EmergencyContact;
use App\Models\User;
use App\Models\Vehicle;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(30)->create(
            [
                'role_id'=>1,
            ]
        );
        Emergency::factory()->count(10)->create()->each(function (){
            User::factory()->create(
                ['role_id'=>3]
            );
        });
        EmergencyContact::factory()->count(100)->create();
        Vehicle::factory()->count(50)->create();
        Accident::factory()->count(30)->create();
    }
}
