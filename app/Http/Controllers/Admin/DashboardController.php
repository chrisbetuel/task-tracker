<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Enums\TaskStatus;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'departments_count' => Department::count(),
            'users_count' => User::count(),
            'projects_count' => Project::count(),
            'tasks_count' => Task::count(),
            'tasks_by_status' => Task::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
        ];

        $recentDepartments = Department::withCount('users')->latest()->take(5)->get();
        $recentUsers = User::with('department')->latest()->take(5)->get();

        $departmentProgress = Department::withCount('projects')
            ->get()
            ->map(function ($dept) {
                $total = Task::where('department_id', $dept->id)->count();
                $done = Task::where('department_id', $dept->id)
                    ->where('status', TaskStatus::Done)->count();
                $blocked = Task::where('department_id', $dept->id)
                    ->where('status', TaskStatus::Blocked)->count();
                return [
                    'department' => $dept,
                    'total_tasks' => $total,
                    'done_tasks' => $done,
                    'blocked_tasks' => $blocked,
                    'completion_rate' => $total > 0 ? round(($done / $total) * 100, 1) : 0,
                ];
            });

        $memberProgress = User::where('role', 'team_member')
            ->with('department')
            ->withCount(['assignedTasks', 'timeLogs'])
            ->get()
            ->map(function ($user) {
                $done = Task::where('assigned_to', $user->id)
                    ->where('status', TaskStatus::Done)->count();
                $total = $user->assigned_tasks_count;
                return [
                    'user' => $user,
                    'total_tasks' => $total,
                    'done_tasks' => $done,
                    'time_entries' => $user->time_logs_count,
                    'completion_rate' => $total > 0 ? round(($done / $total) * 100, 1) : 0,
                ];
            })->sortByDesc('completion_rate')->values();

        $recentProjects = Project::with('department')
            ->withCount('tasks')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($project) {
                $doneCount = Task::where('project_id', $project->id)
                    ->where('status', TaskStatus::Done)->count();
                return [
                    'project' => $project,
                    'total_tasks' => $project->tasks_count,
                    'done_tasks' => $doneCount,
                    'completion_rate' => $project->tasks_count > 0
                        ? round(($doneCount / $project->tasks_count) * 100, 1) : 0,
                ];
            });

        $departmentTypeCounts = Department::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return view('admin.dashboard', compact(
            'stats', 'recentDepartments', 'recentUsers',
            'departmentProgress', 'memberProgress', 'recentProjects',
            'departmentTypeCounts'
        ));
    }
}
