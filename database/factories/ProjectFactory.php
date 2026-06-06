<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'parent_project_id' => null,
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->optional()->paragraph(),
            'url' => fake()->optional(0.4)->url(),
            'created_by' => User::factory()->manager(),
        ];
    }

    public function childOf($parentProject): static
    {
        return $this->state(fn(array $attributes) => [
            'parent_project_id' => $parentProject,
            'department_id' => $parentProject->department_id,
        ]);
    }
}
