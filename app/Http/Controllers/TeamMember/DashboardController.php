<?php

namespace App\Http\Controllers\TeamMember;

use App\Enums\ApprovalStatus;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Services\ReportingService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(ReportingService $reporting)
    {
        $user = request()->user();

        $myTasks = Task::where('assigned_to', $user->id)
            ->with(['project', 'currentAssignment.assigner', 'creator', 'client', 'campaign'])
            ->latest()
            ->get();

        $unclaimedTasks = Task::where('department_id', $user->department_id)
            ->whereNull('assigned_to')
            ->where('status', TaskStatus::PendingAccept)
            ->with('project')
            ->latest()
            ->take(10)
            ->get();

        $teammates = User::where('department_id', $user->department_id)
            ->where('role', 'team_member')
            ->where('id', '!=', $user->id)
            ->withCount('assignedTasks')
            ->get();

        $stats = [
            'total_tasks' => $myTasks->count(),
            'done_tasks' => $myTasks->where('status', TaskStatus::Done->value)->count(),
            'in_progress' => $myTasks->where('status', TaskStatus::InProgress->value)->count(),
            'blocked' => $myTasks->where('status', TaskStatus::Blocked->value)->count(),
        ];

        $acceptanceRate = $reporting->teamMemberAcceptanceRate($user);
        $department = $user->department;

        if ($department->isMarketing()) {
            return $this->marketingDashboard($user, $department, $myTasks, $unclaimedTasks, $teammates, $stats, $acceptanceRate);
        }

        if ($department->isAgent()) {
            return $this->agentDashboard($user, $department, $myTasks, $unclaimedTasks, $teammates, $stats, $acceptanceRate);
        }

        return view('member.dashboard', compact('myTasks', 'unclaimedTasks', 'teammates', 'stats', 'acceptanceRate'));
    }

    private function marketingDashboard($user, $department, $myTasks, $unclaimedTasks, $teammates, $stats, $acceptanceRate)
    {
        $myApprovals = Task::where('department_id', $department->id)
            ->whereIn('approval_status', [ApprovalStatus::Review, ApprovalStatus::Draft])
            ->where('assigned_to', $user->id)
            ->with('project')
            ->latest()
            ->get();

        $calendarTasks = Task::where('assigned_to', $user->id)
            ->whereNotNull('due_date')
            ->whereIn('status', [TaskStatus::PendingAccept, TaskStatus::Accepted, TaskStatus::InProgress])
            ->with('project')
            ->orderBy('due_date')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start' => $task->due_date->format('Y-m-d'),
                    'status' => $task->status->value,
                    'priority' => $task->priority->value,
                    'project' => $task->project?->name,
                ];
            });

        return view('member.marketing-dashboard', compact(
            'myTasks', 'unclaimedTasks', 'teammates', 'stats', 'acceptanceRate',
            'myApprovals', 'calendarTasks', 'department'
        ));
    }

    private function agentDashboard($user, $department, $myTasks, $unclaimedTasks, $teammates, $stats, $acceptanceRate)
    {
        $mySlaTasks = $myTasks->filter(function ($task) {
            return $task->sla_response_minutes !== null;
        })->values();

        $todayResolved = Task::where('assigned_to', $user->id)
            ->where('status', TaskStatus::Done)
            ->whereDate('updated_at', today())
            ->count();

        $avgResponseTime = Task::where('assigned_to', $user->id)
            ->whereNotNull('first_responded_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_responded_at)) as avg_mins')
            ->value('avg_mins');

        $mySlaBreaches = Task::where('assigned_to', $user->id)
            ->whereIn('status', [TaskStatus::PendingAccept, TaskStatus::Accepted, TaskStatus::InProgress])
            ->whereNotNull('sla_response_minutes')
            ->whereNull('first_responded_at')
            ->where(DB::raw('DATE_ADD(created_at, INTERVAL sla_response_minutes MINUTE)'), '<', now())
            ->count();

        $myRating = DB::table('task_ratings')
            ->join('tasks', 'tasks.id', '=', 'task_ratings.task_id')
            ->where('tasks.assigned_to', $user->id)
            ->avg('task_ratings.rating');

        $agentStats = [
            'today_resolved' => $todayResolved,
            'avg_response_mins' => $avgResponseTime ? round($avgResponseTime, 1) : null,
            'sla_breaches' => $mySlaBreaches,
            'avg_rating' => $myRating ? round($myRating, 1) : null,
        ];

        return view('member.agent-dashboard', compact(
            'myTasks', 'unclaimedTasks', 'teammates', 'stats', 'acceptanceRate',
            'mySlaTasks', 'agentStats', 'department'
        ));
    }
}
