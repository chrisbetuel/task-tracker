@extends('layouts.manager')

@section('title', 'Marketing Dashboard')

@section('manager-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-megaphone"></i> {{ $department->name }} — Marketing Dashboard</h2>
    <div>
        <a href="{{ route('manager.tasks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Task
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body text-center">
                <h6>Projects</h6>
                <p class="display-6">{{ $stats['projects_count'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body text-center">
                <h6>Tasks</h6>
                <p class="display-6">{{ $stats['tasks_count'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info">
            <div class="card-body text-center">
                <h6>Team Members</h6>
                <p class="display-6">{{ $stats['team_members_count'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body text-center">
                <h6>Content Output (30d)</h6>
                <p class="display-6">{{ $contentOutput }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-megaphone"></i> Campaigns</span>
                <small class="text-muted">{{ $campaigns->count() }} active campaigns</small>
            </div>
            <div class="card-body">
                @if($campaigns->count())
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Tasks</th>
                                <th>Progress</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campaigns as $c)
                            <tr>
                                <td>
                                    <strong>{{ $c['campaign']->name }}</strong>
                                    @if($c['campaign']->start_date)
                                    <br><small class="text-muted">{{ $c['campaign']->start_date->format('M d') }} - {{ $c['campaign']->end_date?->format('M d') ?? 'TBD' }}</small>
                                    @endif
                                </td>
                                <td>{{ $c['total_tasks'] }}</td>
                                <td style="min-width:150px;">
                                    <div class="progress" style="height:20px;">
                                        <div class="progress-bar bg-success" style="width: {{ $c['completion_rate'] }}%">
                                            {{ $c['completion_rate'] }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $c['campaign']->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($c['campaign']->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted mb-0">No campaigns yet.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">Channels</div>
            <div class="card-body">
                @if(count($channelStats))
                <div class="list-group list-group-flush">
                    @foreach($channelStats as $channel => $count)
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span>
                            <i class="bi bi-{{ $channel === 'email' ? 'envelope' : ($channel === 'social' ? 'share' : ($channel === 'paid_ads' ? 'cash' : ($channel === 'seo' ? 'search' : 'circle'))) }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $channel)) }}
                        </span>
                        <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted mb-0">No channel data.</p>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <i class="bi bi-check-circle"></i> Pending Approvals
                <span class="badge bg-warning ms-1">{{ $pendingApprovals->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($pendingApprovals->count())
                <ul class="list-group list-group-flush">
                    @foreach($pendingApprovals as $task)
                    <li class="list-group-item py-2">
                        <div class="d-flex justify-content-between">
                            <span>{{ $task->title }}</span>
                            <span class="badge {{ $task->approval_status->badgeClass() }}" style="font-size:0.6rem;">{{ $task->approval_status->label() }}</span>
                        </div>
                        <small class="text-muted">{{ $task->project?->name }} · by {{ $task->creator?->name }}</small>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted p-3 mb-0">No pending approvals.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if($upcomingDeadlines->count())
<div class="card mb-4 border-{{ $upcomingDeadlines->where('isOverdue', true)->count() ? 'danger' : 'warning' }}">
    <div class="card-header text-bg-{{ $upcomingDeadlines->where('isOverdue', true)->count() ? 'danger' : 'warning' }}">
        <i class="bi bi-clock"></i> Upcoming Deadlines (next 7 days)
        <span class="badge bg-light text-dark ms-1">{{ $upcomingDeadlines->count() }}</span>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($upcomingDeadlines as $task)
            <div class="col-md-4 mb-2">
                <div class="border rounded p-2 {{ $task->isOverdue() ? 'bg-danger bg-opacity-10 border-danger' : ($task->isUrgent() ? 'bg-warning bg-opacity-10 border-warning' : '') }}">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $task->title }}</strong>
                        <span class="badge bg-{{ $task->priority->badgeClass() }}" style="font-size:0.6rem;">{{ $task->priority->label() }}</span>
                    </div>
                    <small class="text-muted">{{ $task->project?->name }}</small>
                    <div class="small {{ $task->isOverdue() ? 'text-danger fw-bold' : ($task->isUrgent() ? 'text-warning fw-bold' : '') }}">
                        <i class="bi bi-calendar"></i> Due: {{ $task->due_date->format('M d, Y') }}
                        @if($task->isOverdue())
                            <span class="badge bg-danger">OVERDUE</span>
                        @elseif($task->isUrgent())
                            <span class="badge bg-warning text-dark">DUE TODAY</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">Task Status Distribution</div>
    <div class="card-body">
        <div class="row">
            @foreach($stats['status_distribution'] as $status => $data)
            <div class="col-md-2 col-4 mb-2">
                <div class="card text-center">
                    <div class="card-body py-2">
                        <h6 class="card-title small">{{ $data['label'] }}</h6>
                        <p class="h3 mb-0">{{ $data['count'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
