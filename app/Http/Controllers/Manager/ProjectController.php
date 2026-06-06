<?php

namespace App\Http\Controllers\Manager;

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
    public function index()
    {
        $department = request()->user()->department;

        $projects = Project::where('department_id', $department->id)
            ->whereNull('parent_project_id')
            ->withCount('tasks')
            ->with(['children' => function ($q) {
                $q->withCount('tasks');
            }])
            ->latest()
            ->paginate(15);

        return view('manager.projects.index', compact('projects'));
    }

    public function create()
    {
        $department = request()->user()->department;
        $projects = Project::where('department_id', $department->id)
            ->whereNull('parent_project_id')
            ->get();
        $teamMembers = User::where('department_id', $department->id)
            ->where('role', 'team_member')
            ->get();

        return view('manager.projects.create', compact('projects', 'teamMembers'));
    }

    public function store(StoreProjectRequest $request)
    {
        $department = request()->user()->department;

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

        return redirect()->route('manager.projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project, ReportingService $reporting)
    {
        $department = request()->user()->department;

        if ($project->department_id !== $department->id) {
            abort(403);
        }

        $progress = $reporting->projectProgress($project);
        $timeSpent = $reporting->timeSpentByProject($project);

        $project->load(['tasks' => function ($q) {
            $q->with(['assignee', 'activeBlockages', 'assets'])->latest();
        }, 'children']);

        return view('manager.projects.show', compact('project', 'progress', 'timeSpent'));
    }

    public function edit(Project $project)
    {
        $department = request()->user()->department;

        if ($project->department_id !== $department->id) {
            abort(403);
        }

        $project->load(['children' => function ($q) {
            $q->withCount('tasks');
        }]);

        $projects = Project::where('department_id', $department->id)
            ->whereNull('parent_project_id')
            ->where('id', '!=', $project->id)
            ->get();
        $teamMembers = User::where('department_id', $department->id)
            ->where('role', 'team_member')
            ->get();

        return view('manager.projects.edit', compact('project', 'projects', 'teamMembers'));
    }

    public function update(StoreProjectRequest $request, Project $project)
    {
        $department = request()->user()->department;

        if ($project->department_id !== $department->id) {
            abort(403);
        }

        $project->update($request->validated());

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

        return redirect()->route('manager.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $department = request()->user()->department;

        if ($project->department_id !== $department->id) {
            abort(403);
        }

        $project->delete();

        return redirect()->route('manager.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
