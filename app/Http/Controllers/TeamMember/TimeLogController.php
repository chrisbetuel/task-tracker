<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TimeLog;
use Illuminate\Http\Request;

class TimeLogController extends Controller
{
    public function create(Task $task)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        return view('member.time-logs.create', compact('task'));
    }

    public function store(Request $request)
    {
        $user = request()->user();

        $validated = $request->validate([
            'task_id' => ['required', 'string', 'exists:tasks,id'],
            'minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'logged_date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $task = Task::findOrFail($validated['task_id']);

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        TimeLog::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'minutes' => $validated['minutes'],
            'logged_date' => $validated['logged_date'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('member.tasks.my-tasks')
            ->with('success', 'Time logged successfully.');
    }
}
