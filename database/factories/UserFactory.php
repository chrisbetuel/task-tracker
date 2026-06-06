<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::TeamMember,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => UserRole::Admin,
            'department_id' => null,
        ]);
    }

    public function manager(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => UserRole::Manager,
        ]);
    }

    public function teamMember(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => UserRole::TeamMember,
        ]);
    }
}
