<?php

namespace Database\Factories;

use App\Models\Campus;
use App\Models\Department;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
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
            'staff_id' => fake()->unique()->numerify('STAFF#####'),
            'title' => fake()->title(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'university_id' => University::factory(),
            'campus_id' => Campus::factory(),
            'department_id' => Department::factory(),
            'staff_category' => fake()->randomElement(['academic', 'administrative', 'supporting']),
            'joining_date' => fake()->date(),
            'status' => 'active',
            'password_change_required' => false,
            // EnsurePasswordIsChanged treats a missing timestamp as an
            // expired password, so without this every factory user is bounced
            // to the profile page on their first request.
            'password_changed_at' => now(),
        ];
    }
}
