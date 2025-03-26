<?php

namespace Database\Factories;

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
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'dob' => $this->faker->date,
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'avatar' => 'avatars/default.png',
            'role' => $this->faker->randomElement(['owner', 'manager', 'staff', 'chef', 'cashier']),
            'salary' => $this->faker->randomFloat(2, 5000000, 20000000),
            'hire_date' => $this->faker->date,
            'status' => $this->faker->randomElement(['active', 'inactive', 'terminated']),
            'remember_token' => Str::random(10),
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
