<?php

namespace App\Services;

use App\Enums\AssignmentStatus;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TaskWorkflowService
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function assign(Task $task, User $assignee, User $assigner): Task
    {
        return DB::transaction(function () use ($task, $assignee, $assigner) {
            $task->update([
                'status' => TaskStatus::PendingAccept,
                'assigned_to' => $assignee->id,
            ]);

            TaskAssignment::create([
                'task_id' => $task->id,
                'user_id' => $assignee->id,
                'assigned_by' => $assigner->id,
                'status' => AssignmentStatus::Assigned,
                'assigned_at' => now(),
            ]);

            $this->auditService->log('task_assigned', $task, [
                'assigned_to' => $assignee->id,
                'assigned_by' => $assigner->id,
            ]);

            return $task->fresh();
        });
    }

    public function accept(Task $task, User $user): Task
    {
        $this->ensureAssignee($task, $user);

        return DB::transaction(function () use ($task, $user) {
            $task->update([
                'status' => TaskStatus::Accepted,
            ]);

            $this->recordAssignmentStatus($task, $user, AssignmentStatus::Accepted);

            $this->auditService->log('task_accepted', $task, [
                'user_id' => $user->id,
            ]);

            return $task->fresh();
        });
    }

    public function reject(Task $task, User $user, string $reason): Task
    {
        $this->ensureAssignee($task, $user);

        return DB::transaction(function () use ($task, $user, $reason) {
            $task->update([
                'status' => TaskStatus::Rejected,
                'assigned_to' => null,
            ]);

            $this->recordAssignmentStatus($task, $user, AssignmentStatus::Rejected, $reason);

            $this->auditService->log('task_rejected', $task, [
                'user_id' => $user->id,
                'rejection_reason' => $reason,
            ]);

            return $task->fresh();
        });
    }

    public function startWork(Task $task, User $user): Task
    {
        $this->ensureAssignee($task, $user);

        return DB::transaction(function () use ($task, $user) {
            $task->update([
                'status' => TaskStatus::InProgress,
            ]);

            $this->auditService->log('task_started', $task, [
                'user_id' => $user->id,
            ]);

            return $task->fresh();
        });
    }

    public function reportBlockage(Task $task, User $user, string $reason): Task
    {
        return DB::transaction(function () use ($task, $user, $reason) {
            $task->update([
                'status' => TaskStatus::Blocked,
            ]);

            $task->blockages()->create([
                'reported_by' => $user->id,
                'reason' => $reason,
            ]);

            $this->auditService->log('blockage_reported', $task, [
                'user_id' => $user->id,
                'reason' => $reason,
            ]);

            return $task->fresh();
        });
    }

    public function resolveBlockage(Task $task, User $user): Task
    {
        return DB::transaction(function () use ($task, $user) {
            $activeBlockage = $task->activeBlockages()->first();

            if ($activeBlockage) {
                $activeBlockage->update([
                    'resolved_by' => $user->id,
                    'resolved_at' => now(),
                ]);
            }

            $task->update([
                'status' => TaskStatus::InProgress,
            ]);

            $this->auditService->log('blockage_resolved', $task, [
                'user_id' => $user->id,
            ]);

            return $task->fresh();
        });
    }

    public function markDone(Task $task, User $user): Task
    {
        $this->ensureAssignee($task, $user);

        if (!$task->hasTimeLogged()) {
            throw new \RuntimeException('Time must be logged before marking a task as done.');
        }

        return DB::transaction(function () use ($task, $user) {
            $task->update([
                'status' => TaskStatus::Done,
            ]);

            $this->auditService->log('task_completed', $task, [
                'user_id' => $user->id,
            ]);

            return $task->fresh();
        });
    }

    public function reopen(Task $task, User $user): Task
    {
        return DB::transaction(function () use ($task, $user) {
            $task->update([
                'status' => TaskStatus::InProgress,
            ]);

            $this->auditService->log('task_reopened', $task, [
                'user_id' => $user->id,
            ]);

            return $task->fresh();
        });
    }

    public function unassign(Task $task, User $user): Task
    {
        $this->ensureAssignee($task, $user);

        return DB::transaction(function () use ($task, $user) {
            $task->update([
                'status' => TaskStatus::PendingAccept,
                'assigned_to' => null,
            ]);

            $this->recordAssignmentStatus($task, $user, AssignmentStatus::Unassigned);

            $this->auditService->log('task_unassigned', $task, [
                'user_id' => $user->id,
            ]);

            return $task->fresh();
        });
    }

    private function ensureAssignee(Task $task, User $user): void
    {
        if ($task->assigned_to !== $user->id) {
            throw new \RuntimeException('User is not the assignee of this task.');
        }
    }

    private function recordAssignmentStatus(Task $task, User $user, AssignmentStatus $status, ?string $reason = null): void
    {
        $assignment = $task->currentAssignment;

        if ($assignment && $assignment->user_id === $user->id) {
            $assignment->update([
                'status' => $status,
                'rejection_reason' => $reason,
            ]);
        }
    }
}
