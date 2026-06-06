<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\ReportingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingTest extends TestCase
{
    use RefreshDatabase;

    private ReportingService $reporting;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reporting = $this->app->make(ReportingService::class);
    }

    public function test_project_progress(): void
    {
        $department = Department::factory()->create();
        $manager = User::factory()->manager()->create(['department_id' => $department->id]);
        $member = User::factory()->teamMember()->create(['department_id' => $department->id]);
        $project = Project::factory()->create([
            'department_id' => $department->id,
            'created_by' => $manager->id,
        ]);

        Task::factory()->count(3)->create([
            'project_id' => $project->id,
            'department_id' => $department->id,
            'created_by' => $manager->id,
            'assigned_to' => $member->id,
        ]);

        $progress = $this->reporting->projectProgress($project);

        $this->assertEquals(3, $progress['total_tasks']);
        $this->assertEquals(0, $progress['done_tasks']);
        $this->assertEquals(0, $progress['completion_percentage']);
    }

    public function test_team_member_acceptance_rate(): void
    {
        $member = User::factory()->teamMember()->create();

        $rate = $this->reporting->teamMemberAcceptanceRate($member);

        $this->assertArrayHasKey('total_assignments', $rate);
        $this->assertArrayHasKey('acceptance_rate', $rate);
    }

    public function test_task_status_distribution(): void
    {
        $department = Department::factory()->create();

        $distribution = $this->reporting->taskStatusDistribution($department);

        $this->assertArrayHasKey('pending_accept', $distribution);
        $this->assertArrayHasKey('done', $distribution);
    }
}
