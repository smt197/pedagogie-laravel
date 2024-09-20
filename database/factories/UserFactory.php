<?php

namespace Database\Factories;

use App\Enums\ActiveEnum;
use App\Enums\StatutEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'login' => $this->faker->unique()->email(),
            'password' => Hash::make('Passer@123'), 
            'fonction' => $this->faker->jobTitle(),
            'statut' => $this->faker->randomElement([StatutEnum::ACTIF->value, StatutEnum::BLOQUER->value]),
            'role_id' => $this->faker->numberBetween(1, 5),
            'photo' => 'https://via.placeholder.com/640x480.png'
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */

     public function admin()
     {
        return $this->state(fn (array $attributes) => [
            'role_id' => 1,
        ]);
    }

}
