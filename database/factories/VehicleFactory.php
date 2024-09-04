<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'brand' => $this->faker->company,
            'model' => $this->faker->word,
            'color' => $this->faker->safeColorName,
            'license' => $this->faker->bothify('??-###-??'),
            'places'=>$this->faker->numberBetween(2,50)
        ];
    }
}
