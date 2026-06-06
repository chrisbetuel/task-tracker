<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Services\ReportingService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('department')->whereNull('parent_project_id');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhereHas('children', function ($cq) use ($s) {
                      $cq->where('name', 'like', "%{$s}%")
                         ->orWhere('description', 'like', "%{$s}%");
                  });
            });
        }

        $projects = $query->withCount('tasks')
            ->with(['children' => function ($q) {
                $q->withCount('tasks');
            }])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $departments = \App\Models\Department::all();

        return view('admin.projects.index', compact('projects', 'departments'));
    }

    public function show(Project $project, ReportingService $reporting)
    {
        $project->load(['department', 'children.tasks', 'tasks' => function ($q) {
            $q->with(['assignee', 'activeBlockages', 'timeLogs'])->latest();
        }]);

        $progress = $reporting->projectProgress($project);
        $timeSpent = $reporting->timeSpentByProject($project);

        $projectIds = array_merge([$project->id], $project->allDescendantIds());

        $memberStats = \App\Models\User::where('department_id', $project->department_id)
            ->where('role', 'team_member')
            ->get()
            ->map(function ($user) use ($projectIds) {
                $tasks = Task::whereIn('project_id', $projectIds)
                    ->where('assigned_to', $user->id);
                $total = (clone $tasks)->count();
                $done = (clone $tasks)->where('status', TaskStatus::Done)->count();
                $taskIds = (clone $tasks)->pluck('id');
                $minutes = \App\Models\TimeLog::whereIn('task_id', $taskIds)
                    ->where('user_id', $user->id)
                    ->sum('minutes');
                return [
                    'user' => $user,
                    'total_tasks' => $total,
                    'done_tasks' => $done,
                    'completion_rate' => $total > 0 ? round(($done / $total) * 100, 1) : 0,
                    'hours_logged' => round($minutes / 60, 1),
                ];
            })->filter(fn($m) => $m['total_tasks'] > 0)->values();

        $assets = \App\Models\Asset::whereIn('project_id', $projectIds)
            ->orWhereIn('task_id', function ($q) use ($projectIds) {
                $q->select('id')->from('tasks')->whereIn('project_id', $projectIds);
            })
            ->with(['creator', 'task'])
            ->latest()
            ->get();

        return view('admin.projects.show', compact(
            'project', 'progress', 'timeSpent', 'memberStats', 'assets'
        ));
    }
}
