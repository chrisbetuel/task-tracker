<?php

namespace App\Http\Controllers\TeamMember;

use App\Enums\TaskPriority;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\AssignTaskRequest;
use App\Http\Requests\Task\RejectTaskRequest;
use App\Http\Requests\Task\ReportBlockageRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskWorkflowService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function show(Task $task)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        $task->load(['project', 'assignee', 'creator', 'parent', 'children' => function ($q) {
            $q->with('assignee')->latest();
        }, 'assignments' => function ($q) {
            $q->with('user', 'assigner')->latest();
        }, 'activeBlockages' => function ($q) {
            $q->with('reporter');
        }, 'timeLogs' => function ($q) {
            $q->with('user')->latest();
        }, 'assets' => function ($q) {
            $q->with('creator')->latest();
        }]);

        return view('member.tasks.show', compact('task'));
    }

    public function setStatus(Task $task, Request $request)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        $validStatuses = ['pending_accept', 'in_progress', 'done'];

        $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', $validStatuses)],
        ]);

        $task->update(['status' => $request->status]);

        return redirect()->route('member.tasks.show', $task)
            ->with('success', 'Task status updated to ' . str_replace('_', ' ', $request->status) . '.');
    }

    public function myTasks()
    {
        $user = request()->user();

        $tasks = Task::where('assigned_to', $user->id)
            ->with(['project', 'activeBlockages', 'timeLogs'])
            ->latest()
            ->paginate(15);

        return view('member.tasks.my-tasks', compact('tasks'));
    }

    public function create()
    {
        $user = request()->user();

        if ($user->isAdmin()) {
            $projects = Project::with('department')->get();
        } else {
            $projects = Project::where('department_id', $user->department_id)->get();
        }

        $priorities = TaskPriority::cases();
        $parentTask = request('parent_task_id') ? Task::find(request('parent_task_id')) : null;

        return view('member.tasks.create', compact('projects', 'priorities', 'parentTask'));
    }

    public function store(StoreTaskRequest $request)
    {
        $user = request()->user();
        $project = Project::findOrFail($request->project_id);

        if (!$user->isAdmin() && $project->department_id !== $user->department_id) {
            abort(403);
        }

        $task = Task::create([
            'project_id' => $project->id,
            'department_id' => $project->department_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
            'estimated_minutes' => $request->estimated_minutes,
            'parent_task_id' => $request->parent_task_id,
            'created_by' => $user->id,
        ]);

        if ($request->boolean('assign_self')) {
            app(TaskWorkflowService::class)->assign($task, $user, $user);
        }

        return redirect()->route('member.tasks.my-tasks')
            ->with('success', 'Task created successfully.');
    }

    public function unassigned()
    {
        $user = request()->user();

        $tasks = Task::where('department_id', $user->department_id)
            ->whereNull('assigned_to')
            ->with('project')
            ->latest()
            ->paginate(15);

        $teammates = User::where('department_id', $user->department_id)
            ->where('role', 'team_member')
            ->where('id', '!=', $user->id)
            ->get();

        return view('member.tasks.unassigned', compact('tasks', 'teammates'));
    }

    public function edit(Task $task)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        $priorities = TaskPriority::cases();

        return view('member.tasks.edit', compact('task', 'priorities'));
    }

    public function update(Task $task, UpdateTaskRequest $request)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        $task->update($request->validated());

        return redirect()->route('member.tasks.my-tasks')
            ->with('success', 'Task updated successfully.');
    }

    public function claim(Task $task, TaskWorkflowService $workflow)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        if ($task->assigned_to !== null) {
            return back()->with('error', 'Task is already assigned.');
        }

        $workflow->assign($task, $user, $user);

        return redirect()->route('member.tasks.my-tasks')
            ->with('success', 'Task claimed successfully.');
    }

    public function accept(Task $task, TaskWorkflowService $workflow)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        $workflow->accept($task, $user);

        return back()->with('success', 'Task accepted successfully.');
    }

    public function reject(Task $task, RejectTaskRequest $request, TaskWorkflowService $workflow)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        $workflow->reject($task, $user, $request->reason);

        return redirect()->route('member.tasks.my-tasks')
            ->with('success', 'Task rejected.');
    }

    public function start(Task $task, TaskWorkflowService $workflow)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        $workflow->startWork($task, $user);

        return back()->with('success', 'Task started.');
    }

    public function reportBlockage(Task $task, ReportBlockageRequest $request, TaskWorkflowService $workflow)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        $workflow->reportBlockage($task, $user, $request->reason);

        return back()->with('success', 'Blockage reported.');
    }

    public function resolveBlockage(Task $task, TaskWorkflowService $workflow)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        $workflow->resolveBlockage($task, $user);

        return back()->with('success', 'Blockage resolved.');
    }

    public function markDone(Task $task, TaskWorkflowService $workflow)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        try {
            $workflow->markDone($task, $user);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Task marked as done.');
    }

    public function reopen(Task $task, TaskWorkflowService $workflow)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        $workflow->reopen($task, $user);

        return back()->with('success', 'Task reopened.');
    }

    public function assignToMember(Task $task, AssignTaskRequest $request, TaskWorkflowService $workflow)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        if ($task->assigned_to !== null) {
            return back()->with('error', 'Task is already assigned.');
        }

        $assignee = User::findOrFail($request->user_id);

        if ($assignee->department_id !== $user->department_id) {
            abort(403);
        }

        $workflow->assign($task, $assignee, $user);

        return redirect()->route('member.tasks.unassigned')
            ->with('success', "Task assigned to {$assignee->name}.");
    }

    public function unassign(Task $task, TaskWorkflowService $workflow)
    {
        $user = request()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        $workflow->unassign($task, $user);

        return redirect()->route('member.tasks.unassigned')
            ->with('success', 'Task returned to unassigned pool.');
    }
}
