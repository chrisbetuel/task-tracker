<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    public function creating(Task $task): void
    {
        if ($task->department_id === null && $task->project) {
            $task->department_id = $task->project->department_id;
        }
    }

    public function created(Task $task): void
    {
        //
    }

    public function updated(Task $task): void
    {
        //
    }

    public function deleted(Task $task): void
    {
        //
    }

    public function restored(Task $task): void
    {
        //
    }
}
