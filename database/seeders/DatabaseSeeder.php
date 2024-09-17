<?php

namespace Database\Seeders;

use App\Models\Accident;
use App\Models\Emergency;
use App\Models\EmergencyContact;
use App\Models\Position;
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
        User::factory()->create(
            [
                'username'=>'adminUser',
                'email'=>'admin@example.com',
                'role_id'=>2
            ]
        );
        User::factory()->create(
            [
                'username'=>'user',
                'email'=>'user@example.com',
                'role_id'=>1
            ]
        );
        Emergency::factory()->create()->each(function ($emergency){
            User::factory()->create(
                [
                    'username'=>'emergencyAdmin',
                    'email'=>'emergency@example.com',
                    'role_id'=>3,
                    'emergency_id'=>$emergency->id
                ]

            );
            User::factory()->count(5)->create(
                [
                    'role_id'=>4,
                    'emergency_id'=>$emergency->id,'position_id'=>Position::inRandomOrder()->first()->id
                ]
            );
        });
        User::factory()->count(10)->create(
            [
                'role_id'=>1,
            ]
        )->each(function($user){
            Vehicle::factory()->count(10)->create(
                ['user_id'=>$user->id]
            );
        });
        Emergency::factory()->count(10)->create()->each(function ($emergency){
            User::factory()->create(
                ['role_id'=>3,'emergency_id'=>$emergency->id]
            );
            User::factory()->count(5)->create(
                ['role_id'=>4,'emergency_id'=>$emergency->id,'position_id'=>Position::inRandomOrder()->first()->id]
            );
        });
        EmergencyContact::factory()->count(100)->create();
        Vehicle::factory()->count(10)->create();
        Accident::factory()->count(30)->create();
    }
}
