<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmergencyContact>
 */
class EmergencyContactFactory extends Factory
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
            'name' => $this->faker->name,
            'relation' => $this->faker->word,
            'profesional_situation' => $this->faker->jobTitle,
            'phone_number_1' => $this->faker->phoneNumber,
            'phone_number_2' => $this->faker->phoneNumber,
            'phone_number_3' => $this->faker->phoneNumber,
        ];
    }
}