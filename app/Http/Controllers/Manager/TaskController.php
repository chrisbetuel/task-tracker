<?php

namespace App\Http\Controllers\Manager;

use App\Enums\TaskPriority;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\AssignTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskWorkflowService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $department = request()->user()->department;

        $query = Task::where('department_id', $department->id)
            ->with(['project', 'assignee', 'activeBlockages']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assignee_id')) {
            $query->where('assigned_to', $request->assignee_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('overdue')) {
            $query->whereNotNull('due_date')
                  ->where('due_date', '<', now())
                  ->whereNotIn('status', ['done', 'rejected']);
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['title', 'status', 'priority', 'due_date', 'created_at', 'estimated_minutes'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $tasks = $query->paginate(15)->withQueryString();
        $projects = Project::where('department_id', $department->id)->get();
        $teamMembers = User::where('department_id', $department->id)
            ->where('role', 'team_member')
            ->get();
        $priorities = TaskPriority::cases();

        return view('manager.tasks.index', compact('tasks', 'projects', 'teamMembers', 'priorities'));
    }

    public function create()
    {
        $department = request()->user()->department;
        $projects = Project::where('department_id', $department->id)->get();
        $teamMembers = User::where('department_id', $department->id)
            ->where('role', 'team_member')
            ->get();
        $priorities = TaskPriority::cases();
        $parentTask = request('parent_task_id') ? Task::find(request('parent_task_id')) : null;

        return view('manager.tasks.create', compact('projects', 'teamMembers', 'priorities', 'parentTask'));
    }

    public function store(StoreTaskRequest $request)
    {
        $department = request()->user()->department;

        $task = Task::create([
            'project_id' => $request->project_id,
            'department_id' => $department->id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
            'estimated_minutes' => $request->estimated_minutes,
            'parent_task_id' => $request->parent_task_id,
            'created_by' => request()->user()->id,
        ]);

        if ($request->filled('assigned_to')) {
            $assignee = User::findOrFail($request->assigned_to);
            app(TaskWorkflowService::class)->assign($task, $assignee, request()->user());
        }

        return redirect()->route('manager.tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $department = request()->user()->department;

        if ($task->department_id !== $department->id) {
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

        $teamMembers = User::where('department_id', $department->id)
            ->where('role', 'team_member')
            ->get();

        return view('manager.tasks.show', compact('task', 'teamMembers'));
    }

    public function setStatus(Task $task, Request $request)
    {
        $department = request()->user()->department;

        if ($task->department_id !== $department->id) {
            abort(403);
        }

        $validStatuses = ['pending_accept', 'in_progress', 'done'];

        $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', $validStatuses)],
        ]);

        $task->update(['status' => $request->status]);

        return redirect()->route('manager.tasks.show', $task)
            ->with('success', 'Task status updated to ' . str_replace('_', ' ', $request->status) . '.');
    }

    public function assign(Task $task, AssignTaskRequest $request, TaskWorkflowService $workflow)
    {
        $department = request()->user()->department;

        if ($task->department_id !== $department->id) {
            abort(403);
        }

        $assignee = User::findOrFail($request->user_id);
        $workflow->assign($task, $assignee, request()->user());

        return redirect()->route('manager.tasks.show', $task)
            ->with('success', 'Task assigned successfully.');
    }

    public function destroy(Task $task)
    {
        $department = request()->user()->department;

        if ($task->department_id !== $department->id) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('manager.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
