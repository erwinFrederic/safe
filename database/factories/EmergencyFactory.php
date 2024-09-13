<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Emergency>
 */
class EmergencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\Emergency::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'address' => $this->faker->address,
            'email' => $this->faker->safeEmail,
            'logo' => $this->faker->imageUrl(200, 200, 'business', true), // URL d'une image de logo
            'phone_number_1' => $this->faker->phoneNumber,
            'phone_number_2' => $this->faker->phoneNumber,
            'phone_number_3' => $this->faker->phoneNumber,
        ];
    }
}
