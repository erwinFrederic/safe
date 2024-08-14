<?php

namespace Database\Factories;

use App\Models\Emergency;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accident>
 */
class AccidentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::inRandomOrder()->first()->id,
            'emergency_id' => Emergency::inRandomOrder()->first()->id,
            'longitude' => $this->faker->longitude,
            'latitude' => $this->faker->latitude,
            'report' => $this->faker->paragraph,
        ];
    }
}