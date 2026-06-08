<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskWorkflowService;
use App\Services\ReportingService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private function checkDepartment()
    {
        $department = request()->user()->department;

        if (!$department) {
            abort(403);
        }

        return $department;
    }

    public function index()
    {
        $department = $this->checkDepartment();

        $projects = Project::where('department_id', $department->id)
            ->whereNull('parent_project_id')
            ->withCount('tasks')
            ->with(['children' => function ($q) {
                $q->withCount('tasks');
            }])
            ->latest()
            ->paginate(15);

        return view('member.projects.index', compact('projects'));
    }

    public function create()
    {
        $department = $this->checkDepartment();

        $projects = Project::where('department_id', $department->id)
            ->whereNull('parent_project_id')
            ->get();
        $teamMembers = User::where('department_id', $department->id)
            ->where('role', 'team_member')
            ->get();

        return view('member.projects.create', compact('projects', 'teamMembers'));
    }

    public function store(StoreProjectRequest $request)
    {
        $department = $this->checkDepartment();

        $project = Project::create([
            'department_id' => $department->id,
            'parent_project_id' => $request->parent_project_id,
            'name' => $request->name,
            'description' => $request->description,
            'url' => $request->url,
            'status' => $request->status,
            'created_by' => request()->user()->id,
        ]);

        if ($request->filled('sub_projects')) {
            foreach ($request->sub_projects as $sub) {
                Project::create([
                    'department_id' => $department->id,
                    'parent_project_id' => $project->id,
                    'name' => $sub['name'],
                    'description' => $sub['description'] ?? null,
                    'url' => $sub['url'] ?? null,
                    'created_by' => request()->user()->id,
                ]);
            }
        }

        if ($request->filled('initial_tasks')) {
            foreach ($request->initial_tasks as $taskData) {
                $task = Task::create([
                    'project_id' => $project->id,
                    'department_id' => $department->id,
                    'title' => $taskData['title'],
                    'priority' => $taskData['priority'],
                    'due_date' => $taskData['due_date'] ?? null,
                    'estimated_minutes' => $taskData['estimated_minutes'] ?? null,
                    'description' => "Initial task for project: {$project->name}",
                    'created_by' => request()->user()->id,
                ]);

                if (!empty($taskData['assigned_to'])) {
                    $assignee = User::find($taskData['assigned_to']);
                    if ($assignee) {
                        app(TaskWorkflowService::class)->assign($task, $assignee, request()->user());
                    }
                }
            }
        }

        return redirect()->route('member.projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project, ReportingService $reporting)
    {
        $department = $this->checkDepartment();

        if ($project->department_id !== $department->id) {
            abort(403);
        }

        $progress = $reporting->projectProgress($project);
        $timeSpent = $reporting->timeSpentByProject($project);

        $project->load(['comments' => function ($q) {
            $q->with('user')->latest();
        }, 'tasks' => function ($q) {
            $q->whereNull('parent_task_id')
              ->with(['assignee', 'activeBlockages', 'assets', 'children' => function ($q) {
                  $q->with(['assignee', 'activeBlockages', 'assets'])->latest();
              }])
              ->latest();
        }, 'children']);

        return view('member.projects.show', compact('project', 'progress', 'timeSpent'));
    }
}
