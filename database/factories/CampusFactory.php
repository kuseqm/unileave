<?php

namespace Database\Factories;

use App\Models\Campus;
use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampusFactory extends Factory
{
    protected $model = Campus::class;

    public function definition(): array
    {
        return [
            'university_id' => University::factory(),
            'name' => fake()->city(),
            'address' => fake()->address(),
            'gps_lat' => fake()->latitude(),
            'gps_lng' => fake()->longitude(),
            'code' => fake()->unique()->lexify('CAM???'),
            'status' => 'active',
        ];
    }
}
