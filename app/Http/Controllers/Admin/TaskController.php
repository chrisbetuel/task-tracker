<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TaskPriority;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Models\Project;
use App\Models\Task;

class TaskController extends Controller
{
    public function create()
    {
        $projects = Project::with('department')->latest()->get();
        $priorities = TaskPriority::cases();
        $parentTask = request('parent_task_id') ? Task::find(request('parent_task_id')) : null;

        return view('admin.tasks.create', compact('projects', 'priorities', 'parentTask'));
    }

    public function store(StoreTaskRequest $request)
    {
        $project = Project::findOrFail($request->project_id);

        $task = Task::create([
            'project_id' => $project->id,
            'department_id' => $project->department_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
            'estimated_minutes' => $request->estimated_minutes,
            'parent_task_id' => $request->parent_task_id,
            'created_by' => request()->user()->id,
        ]);

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Task created successfully.');
    }
}
