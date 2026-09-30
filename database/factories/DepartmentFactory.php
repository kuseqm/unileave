<?php

namespace Database\Factories;

use App\Models\Campus;
use App\Models\Department;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'campus_id' => Campus::factory(),
            'school_id' => School::factory(),
            'name' => fake()->randomElement(['Computer Science', 'Mathematics', 'Physics', 'Chemistry', 'Economics']),
            'code' => fake()->unique()->lexify('DEP???'),
            'description' => fake()->sentence(),
            'status' => 'active',
        ];
    }
}
