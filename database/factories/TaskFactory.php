<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Department;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        $project = Project::factory()->create();

        return [
            'project_id' => $project->id,
            'department_id' => $project->department_id,
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => TaskStatus::PendingAccept,
            'assigned_to' => null,
            'created_by' => User::factory()->manager(),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'due_date' => fake()->optional(0.6)->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'estimated_minutes' => fake()->optional(0.7)->numberBetween(30, 4800),
        ];
    }

    public function assignedTo(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'assigned_to' => $user->id,
            'status' => TaskStatus::PendingAccept,
        ]);
    }

    public function withStatus(TaskStatus $status): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => $status,
        ]);
    }
}
