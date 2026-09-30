<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\Student;
use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lastName = fake()->lastName();
        $firstName = fake()->firstName();

        return [
            'university_id' => University::factory(),
            'registration_number' => fake()->unique()->numerify('####/####'),
            'full_name' => "{$lastName} {$firstName}",
            'last_name' => $lastName,
            'first_name' => $firstName,
            'program_id' => Program::factory(),
            'is_graduated' => false,
        ];
    }
}
