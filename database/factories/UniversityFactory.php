<?php

namespace Database\Factories;

use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

class UniversityFactory extends Factory
{
    protected $model = University::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'code' => fake()->unique()->lexify('UNI???'),
            'gps_lat' => fake()->latitude(),
            'gps_lng' => fake()->longitude(),
            'timezone' => 'Africa/Dar_es_Salaam',
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'status' => 'active',
        ];
    }
}
