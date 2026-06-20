@extends('layouts.manager')

@section('title', 'Agent Dashboard')

@section('manager-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-headset"></i> {{ $department->name }} — Agent Dashboard</h2>
    <div>
        <a href="{{ route('manager.tasks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Task
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary stat-card">
            <div class="card-body text-center">
                <h6>Projects</h6>
                <p class="display-6">{{ $stats['projects_count'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success stat-card">
            <div class="card-body text-center">
                <h6>Total Tickets</h6>
                <p class="display-6">{{ $stats['tasks_count'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info stat-card">
            <div class="card-body text-center">
                <h6>Team Members</h6>
                <p class="display-6">{{ $stats['team_members_count'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card {{ $queueStats['sla_breached'] ? 'text-bg-danger' : 'text-bg-warning' }} stat-card">
            <div class="card-body text-center">
                <h6>SLA Breached</h6>
                <p class="display-6">{{ $queueStats['sla_breached'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card h-100 border-warning">
            <div class="card-header text-bg-warning">
                <i class="bi bi-inbox"></i> Unassigned Tickets
                <span class="badge bg-light text-dark ms-1">{{ $queueStats['unassigned'] }}</span>
            </div>
            <div class="card-body p-0">
                @if($unassignedTickets->count())
                <ul class="list-group list-group-flush">
                    @foreach($unassignedTickets as $task)
                    <li class="list-group-item py-2">
                        <div class="d-flex justify-content-between">
                            <span>{{ $task->title }}</span>
                            <span class="badge {{ $task->priority->badgeClass() }}" style="font-size:0.6rem;">{{ $task->priority->label() }}</span>
                        </div>
                        <small class="text-muted">
                            @if($task->client)
                                {{ $task->client->name }}
                            @else
                                {{ $task->project?->name }}
                            @endif
                            · {{ $task->created_at->diffForHumans() }}
                        </small>
                        @if($task->slaDeadline())
                        <div class="small mt-1">
                            <i class="bi bi-clock"></i> SLA: {{ $task->slaDeadline()->diffForHumans() }}
                            @if($task->isSlaBreached())
                                <span class="badge bg-danger">BREACHED</span>
                            @endif
                        </div>
                        @endif
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted p-3 mb-0">No unassigned tickets.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-danger">
            <div class="card-header text-bg-danger">
                <i class="bi bi-exclamation-triangle"></i> Urgent & SLA At Risk
                <span class="badge bg-light text-dark ms-1">{{ $queueStats['urgent'] }}</span>
            </div>
            <div class="card-body">
                @php
                    $urgentTasks = \App\Models\Task::where('department_id', $department->id)
                        ->whereIn('status', ['pending_accept', 'accepted', 'in_progress'])
                        ->where('priority', \App\Enums\TaskPriority::Critical)
                        ->with(['assignee', 'client'])
                        ->latest()
                        ->take(10)
                        ->get();
                @endphp
                @if($urgentTasks->count())
                <ul class="list-group list-group-flush">
                    @foreach($urgentTasks as $task)
                    <li class="list-group-item px-0 py-2">
                        <div class="d-flex justify-content-between">
                            <span><strong>{{ $task->title }}</strong></span>
                            <span class="badge bg-danger" style="font-size:0.6rem;">{{ $task->priority->label() }}</span>
                        </div>
                        <small class="text-muted">
                            Assigned to: {{ $task->assignee?->name ?? 'Unassigned' }}
                            @if($task->slaDeadline())
                                · SLA {{ $task->slaDeadline()->diffForHumans() }}
                            @endif
                        </small>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted mb-0">No urgent tickets.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-bar-chart"></i> Status Distribution
            </div>
            <div class="card-body">
                @foreach($stats['status_distribution'] as $status => $data)
                <div class="d-flex justify-content-between mb-1">
                    <small>{{ $data['label'] }}</small>
                    <span class="badge bg-secondary">{{ $data['count'] }}</span>
                </div>
                @if($data['count'] > 0 && $stats['tasks_count'] > 0)
                <div class="progress mb-2" style="height:6px;">
                    <div class="progress-bar" style="width: {{ round(($data['count'] / max($stats['tasks_count'], 1)) * 100) }}%"></div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-trophy"></i> Team Leaderboard
    </div>
    <div class="card-body">
        @if($teamLeaderboard->count())
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Agent</th>
                        <th>Tickets Resolved</th>
                        <th>Avg Response</th>
                        <th>SLA Breaches</th>
                        <th>Customer Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teamLeaderboard as $idx => $entry)
                    <tr class="{{ $idx === 0 ? 'table-warning' : '' }}">
                        <td>
                            @if($idx === 0)
                                <i class="bi bi-trophy-fill text-warning"></i>
                            @elseif($idx === 1)
                                <i class="bi bi-trophy-fill text-secondary"></i>
                            @elseif($idx === 2)
                                <i class="bi bi-trophy-fill text-danger" style="color:#cd7f32 !important;"></i>
                            @else
                                {{ $idx + 1 }}
                            @endif
                        </td>
                        <td><strong>{{ $entry['user']->name }}</strong></td>
                        <td><span class="badge bg-success fs-6">{{ $entry['resolved'] }}</span></td>
                        <td>
                            @if($entry['avg_response_mins'])
                                @if($entry['avg_response_mins'] < 60)
                                    {{ $entry['avg_response_mins'] }} min
                                @else
                                    {{ round($entry['avg_response_mins'] / 60, 1) }} hr
                                @endif
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($entry['sla_breaches'] > 0)
                                <span class="badge bg-danger">{{ $entry['sla_breaches'] }}</span>
                            @else
                                <span class="badge bg-success">0</span>
                            @endif
                        </td>
                        <td>
                            @if($entry['avg_rating'])
                                <span class="badge bg-info">{{ $entry['avg_rating'] }}/5</span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted mb-0">No team member data available.</p>
        @endif
    </div>
</div>
@endsection
