<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Blockage;
use App\Models\Department;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    public function projectProgress(Project $project): array
    {
        $projectIds = array_merge([$project->id], $project->allDescendantIds());

        $totalTasks = Task::whereIn('project_id', $projectIds)->count();
        $doneTasks = Task::whereIn('project_id', $projectIds)->where('status', TaskStatus::Done)->count();
        $blockedTasks = Task::whereIn('project_id', $projectIds)->where('status', TaskStatus::Blocked)->count();

        return [
            'total_tasks' => $totalTasks,
            'done_tasks' => $doneTasks,
            'blocked_tasks' => $blockedTasks,
            'completion_percentage' => $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100, 1) : 0,
        ];
    }

    public function teamMemberAcceptanceRate(User $user): array
    {
        $totalAssignments = $user->taskAssignments()->count();
        $accepted = $user->taskAssignments()->where('status', 'accepted')->count();
        $rejected = $user->taskAssignments()->where('status', 'rejected')->count();

        return [
            'total_assignments' => $totalAssignments,
            'accepted' => $accepted,
            'rejected' => $rejected,
            'acceptance_rate' => $totalAssignments > 0 ? round(($accepted / $totalAssignments) * 100, 1) : 0,
        ];
    }

    public function blockageAnalysis(Department $department): array
    {
        $blockages = Blockage::whereHas('task', function ($q) use ($department) {
            $q->where('department_id', $department->id);
        })->get();

        $active = $blockages->whereNull('resolved_at');
        $resolved = $blockages->whereNotNull('resolved_at');

        $avgResolutionTime = null;
        if ($resolved->count() > 0) {
            $times = $resolved->map(fn ($b) => $b->resolved_at->diffInHours($b->created_at));
            $avgResolutionTime = $times->avg();
        }

        return [
            'total' => $blockages->count(),
            'active' => $active->count(),
            'resolved' => $resolved->count(),
            'average_resolution_hours' => $avgResolutionTime ? round($avgResolutionTime, 1) : null,
        ];
    }

    public function timeSpentByProject(Project $project): array
    {
        $projectIds = array_merge([$project->id], $project->allDescendantIds());

        $result = TimeLog::whereHas('task', function ($q) use ($projectIds) {
            $q->whereIn('project_id', $projectIds);
        })->select(DB::raw('SUM(minutes) as total_minutes'), DB::raw('COUNT(DISTINCT task_id) as task_count'))
          ->first();

        return [
            'total_minutes' => (int) ($result->total_minutes ?? 0),
            'total_hours' => round(($result->total_minutes ?? 0) / 60, 1),
            'task_count' => (int) ($result->task_count ?? 0),
        ];
    }

    public function overdueTasks(Department $department): array
    {
        return Task::where('department_id', $department->id)
            ->whereIn('status', [TaskStatus::PendingAccept, TaskStatus::Accepted, TaskStatus::InProgress])
            ->where('created_at', '<', now()->subDays(14))
            ->with(['project', 'assignee'])
            ->get()
            ->toArray();
    }

    public function taskStatusDistribution(Department $department): array
    {
        $statuses = Task::where('department_id', $department->id)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $result = [];
        foreach (TaskStatus::cases() as $status) {
            $result[$status->value] = [
                'label' => $status->label(),
                'count' => $statuses[$status->value] ?? 0,
            ];
        }

        return $result;
    }

    public function teammateProfile(User $user, User $viewer): array
    {
        if ($user->department_id !== $viewer->department_id && !$viewer->isAdmin()) {
            throw new \RuntimeException('Cannot view profile of user in another department.');
        }

        $projects = Project::where('department_id', $user->department_id)
            ->with(['tasks' => function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            }])
            ->get();

        $projectData = [];
        foreach ($projects as $project) {
            $tasks = $project->tasks;
            $total = $tasks->count();
            $done = $tasks->where('status', TaskStatus::Done->value)->count();
            $totalMinutes = TimeLog::whereIn('task_id', $tasks->pluck('id'))
                ->where('user_id', $user->id)
                ->sum('minutes');

            $projectData[] = [
                'project' => $project->only(['id', 'name']),
                'total_tasks' => $total,
                'done_tasks' => $done,
                'completion_rate' => $total > 0 ? round(($done / $total) * 100, 1) : 0,
                'total_time_hours' => round($totalMinutes / 60, 1),
                'task_titles' => $tasks->pluck('title'),
            ];
        }

        $activeBlockages = Blockage::whereHas('task', function ($q) use ($user) {
            $q->where('assigned_to', $user->id);
        })->whereNull('resolved_at')
            ->with('task')
            ->get()
            ->map(fn ($b) => [
                'task_title' => $b->task->title,
                'reason' => $b->reason,
                'reported_at' => $b->created_at,
            ]);

        $recentCompletions = Task::where('assigned_to', $user->id)
            ->where('status', TaskStatus::Done)
            ->where('updated_at', '>=', now()->subDays(30))
            ->with('project')
            ->get()
            ->map(fn ($t) => [
                'title' => $t->title,
                'project_name' => $t->project->name,
                'completed_at' => $t->updated_at,
            ]);

        $weeklyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end = now()->subWeeks($i)->endOfWeek();
            $count = TimeLog::where('user_id', $user->id)
                ->whereBetween('logged_date', [$start, $end])
                ->sum('minutes');

            $weeklyTrend[] = [
                'week' => $start->format('M d'),
                'minutes' => (int) $count,
            ];
        }

        return [
            'user' => $user->only(['id', 'name', 'email']),
            'projects' => $projectData,
            'active_blockages' => $activeBlockages,
            'recent_completions' => $recentCompletions,
            'weekly_trend' => $weeklyTrend,
            'acceptance_rate' => $this->teamMemberAcceptanceRate($user),
        ];
    }
}
