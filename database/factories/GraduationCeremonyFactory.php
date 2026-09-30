<?php

namespace Database\Factories;

use App\Models\GraduationCeremony;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GraduationCeremony>
 */
class GraduationCeremonyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'year' => fake()->unique()->numberBetween(2010, 2030),
            'sequence_number' => fake()->numberBetween(1, 20),
            'ceremony_date' => fake()->dateTimeBetween('-2 years', '+2 years')->format('Y-m-d'),
        ];
    }
}
