<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private TaskWorkflowService $workflow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workflow = $this->app->make(TaskWorkflowService::class);
    }

    public function test_assign_sets_pending_accept(): void
    {
        $manager = User::factory()->manager()->create();
        $member = User::factory()->teamMember()->create();
        $task = Task::factory()->create();

        $this->workflow->assign($task, $member, $manager);

        $task->refresh();
        $this->assertEquals(TaskStatus::PendingAccept, $task->status);
        $this->assertEquals($member->id, $task->assigned_to);
    }

    public function test_accept_moves_to_accepted(): void
    {
        $member = User::factory()->teamMember()->create();
        $task = Task::factory()->assignedTo($member)->create();

        $this->workflow->accept($task, $member);

        $task->refresh();
        $this->assertEquals(TaskStatus::Accepted, $task->status);
    }

    public function test_reject_moves_to_rejected_and_unassigns(): void
    {
        $member = User::factory()->teamMember()->create();
        $task = Task::factory()->assignedTo($member)->create();

        $this->workflow->reject($task, $member, 'Too busy');

        $task->refresh();
        $this->assertEquals(TaskStatus::Rejected, $task->status);
        $this->assertNull($task->assigned_to);
    }

    public function test_mark_done_requires_time_logged(): void
    {
        $member = User::factory()->teamMember()->create();
        $task = Task::factory()->assignedTo($member)->withStatus(TaskStatus::InProgress)->create();

        $this->expectException(\RuntimeException::class);
        $this->workflow->markDone($task, $member);
    }

    public function test_full_workflow(): void
    {
        $manager = User::factory()->manager()->create();
        $member = User::factory()->teamMember()->create();
        $task = Task::factory()->create();

        $task = $this->workflow->assign($task, $member, $manager);
        $this->assertEquals(TaskStatus::PendingAccept, $task->status);

        $task = $this->workflow->accept($task, $member);
        $this->assertEquals(TaskStatus::Accepted, $task->status);

        $task = $this->workflow->startWork($task, $member);
        $this->assertEquals(TaskStatus::InProgress, $task->status);

        $task->timeLogs()->create([
            'user_id' => $member->id,
            'minutes' => 60,
            'logged_date' => now(),
        ]);

        $task = $this->workflow->markDone($task, $member);
        $this->assertEquals(TaskStatus::Done, $task->status);

        $task = $this->workflow->reopen($task, $member);
        $this->assertEquals(TaskStatus::InProgress, $task->status);
    }
}
