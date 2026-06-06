<?php

namespace App\Http\Controllers\Manager;

use App\Enums\ApprovalStatus;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Task;
use App\Services\ReportingService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(ReportingService $reporting)
    {
        $user = request()->user();
        $department = $user->department;

        $stats = [
            'projects_count' => $department->projects()->count(),
            'tasks_count' => Task::where('department_id', $department->id)->count(),
            'team_members_count' => $department->teamMembers()->count(),
            'status_distribution' => $reporting->taskStatusDistribution($department),
            'overdue_tasks' => $reporting->overdueTasks($department),
        ];

        $recentProjects = $department->projects()
            ->withCount('tasks')
            ->latest()
            ->take(5)
            ->get();

        if ($department->isMarketing()) {
            return $this->marketingDashboard($department, $stats, $recentProjects, $reporting);
        }

        if ($department->isAgent()) {
            return $this->agentDashboard($department, $stats, $recentProjects, $reporting);
        }

        return view('manager.dashboard', compact('stats', 'recentProjects', 'department'));
    }

    private function marketingDashboard($department, $stats, $recentProjects, $reporting)
    {
        $campaigns = Campaign::where('department_id', $department->id)
            ->withCount('tasks')
            ->latest()
            ->get()
            ->map(function ($campaign) {
                $total = $campaign->tasks_count;
                $done = Task::where('campaign_id', $campaign->id)
                    ->where('status', TaskStatus::Done)->count();
                return [
                    'campaign' => $campaign,
                    'total_tasks' => $total,
                    'done_tasks' => $done,
                    'completion_rate' => $total > 0 ? round(($done / $total) * 100, 1) : 0,
                ];
            });

        $pendingApprovals = Task::where('department_id', $department->id)
            ->whereIn('approval_status', [ApprovalStatus::Review, ApprovalStatus::Approved])
            ->with(['project', 'assignee', 'creator'])
            ->latest()
            ->get();

        $upcomingDeadlines = Task::where('department_id', $department->id)
            ->whereNotNull('due_date')
            ->whereIn('status', [TaskStatus::PendingAccept, TaskStatus::Accepted, TaskStatus::InProgress])
            ->where('due_date', '<=', now()->addDays(7))
            ->with('project')
            ->orderBy('due_date')
            ->get();

        $channelStats = Task::where('department_id', $department->id)
            ->whereNotNull('channel')
            ->select('channel', DB::raw('count(*) as count'))
            ->groupBy('channel')
            ->pluck('count', 'channel')
            ->toArray();

        $contentOutput = Task::where('department_id', $department->id)
            ->where('status', TaskStatus::Done)
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();

        return view('manager.marketing-dashboard', compact(
            'department', 'stats', 'recentProjects',
            'campaigns', 'pendingApprovals', 'upcomingDeadlines',
            'channelStats', 'contentOutput'
        ));
    }

    private function agentDashboard($department, $stats, $recentProjects, $reporting)
    {
        $queueStats = [
            'unassigned' => Task::where('department_id', $department->id)
                ->whereNull('assigned_to')
                ->where('status', TaskStatus::PendingAccept)
                ->count(),
            'urgent' => Task::where('department_id', $department->id)
                ->whereIn('status', [TaskStatus::PendingAccept, TaskStatus::Accepted, TaskStatus::InProgress])
                ->where('priority', \App\Enums\TaskPriority::Critical)
                ->count(),
            'sla_breached' => Task::where('department_id', $department->id)
                ->whereIn('status', [TaskStatus::PendingAccept, TaskStatus::Accepted, TaskStatus::InProgress])
                ->whereNotNull('sla_response_minutes')
                ->whereNull('first_responded_at')
                ->where(DB::raw('DATE_ADD(created_at, INTERVAL sla_response_minutes MINUTE)'), '<', now())
                ->count(),
        ];

        $unassignedTickets = Task::where('department_id', $department->id)
            ->whereNull('assigned_to')
            ->where('status', TaskStatus::PendingAccept)
            ->with(['project', 'client', 'creator'])
            ->latest()
            ->take(10)
            ->get();

        $teamLeaderboard = $department->teamMembers()
            ->withCount(['assignedTasks'])
            ->get()
            ->map(function ($member) {
                $resolved = Task::where('assigned_to', $member->id)
                    ->where('status', TaskStatus::Done)->count();
                $avgResponse = Task::where('assigned_to', $member->id)
                    ->whereNotNull('first_responded_at')
                    ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_responded_at)) as avg_mins')
                    ->value('avg_mins');
                $slaBreaches = Task::where('assigned_to', $member->id)
                    ->whereIn('status', [TaskStatus::PendingAccept, TaskStatus::Accepted, TaskStatus::InProgress])
                    ->whereNotNull('sla_response_minutes')
                    ->whereNull('first_responded_at')
                    ->where(DB::raw('DATE_ADD(created_at, INTERVAL sla_response_minutes MINUTE)'), '<', now())
                    ->count();
                $avgRating = DB::table('task_ratings')
                    ->join('tasks', 'tasks.id', '=', 'task_ratings.task_id')
                    ->where('tasks.assigned_to', $member->id)
                    ->avg('task_ratings.rating');

                return [
                    'user' => $member,
                    'resolved' => $resolved,
                    'avg_response_mins' => $avgResponse ? round($avgResponse, 1) : null,
                    'sla_breaches' => $slaBreaches,
                    'avg_rating' => $avgRating ? round($avgRating, 1) : null,
                ];
            })
            ->sortByDesc('resolved')
            ->values();

        return view('manager.agent-dashboard', compact(
            'department', 'stats', 'recentProjects',
            'queueStats', 'unassignedTickets', 'teamLeaderboard'
        ));
    }
}
