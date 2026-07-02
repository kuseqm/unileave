<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\Campus;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        return [
            'campus_id' => Campus::factory(),
            'name' => fake()->randomElement(['Faculty of Science', 'Faculty of Arts', 'Faculty of Engineering', 'Faculty of Business']),
            'code' => fake()->unique()->lexify('SCH???'),
            'description' => fake()->sentence(),
            'status' => 'active',
        ];
    }
}
