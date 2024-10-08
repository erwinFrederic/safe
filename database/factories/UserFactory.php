<?php

namespace Database\Factories;

use App\Models\Emergency;
use App\Models\Position;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'matricule' => $this->faker->unique()->word,
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->unique()->phoneNumber(),
            'blood_type' => $this->faker->optional()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'sex' => $this->faker->randomElement(['Homme', 'Femme']),
            'birth_date' => $this->faker->optional()->dateTimeBetween('-80 years', '-18 years'),
            'hospital' => json_encode([
                'name' => $this->faker->company(),
                'address' => $this->faker->address(),
                'phone' => $this->faker->phoneNumber(),
            ]),
            'photo' => $this->faker->optional()->imageUrl(200, 200, 'people', true),
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // Password sécurisé
            'remember_token' => Str::random(10),
            //'position_id' => Position::inRandomOrder()->first()->id, // Référence aléatoire à une position existante
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
