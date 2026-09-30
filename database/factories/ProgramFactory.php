<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'name' => 'Bachelor of Science in '.fake()->unique()->word(),
            'study_level' => 'Bachelor',
            'code' => fake()->unique()->lexify('PRG???'),
            'status' => 'active',
        ];
    }
}
