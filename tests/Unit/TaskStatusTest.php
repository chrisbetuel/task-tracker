<?php

namespace Tests\Unit;

use App\Enums\TaskStatus;
use PHPUnit\Framework\TestCase;

class TaskStatusTest extends TestCase
{
    public function test_allowed_transitions_from_pending_accept(): void
    {
        $transitions = TaskStatus::allowedTransitions(TaskStatus::PendingAccept);
        $this->assertCount(2, $transitions);
        $this->assertContains(TaskStatus::Accepted, $transitions);
        $this->assertContains(TaskStatus::Rejected, $transitions);
    }

    public function test_allowed_transitions_from_accepted(): void
    {
        $transitions = TaskStatus::allowedTransitions(TaskStatus::Accepted);
        $this->assertCount(2, $transitions);
        $this->assertContains(TaskStatus::InProgress, $transitions);
        $this->assertContains(TaskStatus::PendingAccept, $transitions);
    }

    public function test_allowed_transitions_from_in_progress(): void
    {
        $transitions = TaskStatus::allowedTransitions(TaskStatus::InProgress);
        $this->assertCount(3, $transitions);
        $this->assertContains(TaskStatus::Blocked, $transitions);
        $this->assertContains(TaskStatus::Done, $transitions);
        $this->assertContains(TaskStatus::PendingAccept, $transitions);
    }

    public function test_allowed_transitions_from_blocked(): void
    {
        $transitions = TaskStatus::allowedTransitions(TaskStatus::Blocked);
        $this->assertCount(1, $transitions);
        $this->assertContains(TaskStatus::InProgress, $transitions);
    }

    public function test_allowed_transitions_from_done(): void
    {
        $transitions = TaskStatus::allowedTransitions(TaskStatus::Done);
        $this->assertCount(1, $transitions);
        $this->assertContains(TaskStatus::InProgress, $transitions);
    }

    public function test_allowed_transitions_from_rejected(): void
    {
        $transitions = TaskStatus::allowedTransitions(TaskStatus::Rejected);
        $this->assertCount(0, $transitions);
    }

    public function test_labels(): void
    {
        $this->assertEquals('Pending Accept', TaskStatus::PendingAccept->label());
        $this->assertEquals('Accepted', TaskStatus::Accepted->label());
        $this->assertEquals('In Progress', TaskStatus::InProgress->label());
        $this->assertEquals('Blocked', TaskStatus::Blocked->label());
        $this->assertEquals('Done', TaskStatus::Done->label());
        $this->assertEquals('Rejected', TaskStatus::Rejected->label());
    }
}
