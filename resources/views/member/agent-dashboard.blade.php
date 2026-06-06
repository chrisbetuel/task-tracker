@extends('layouts.member')

@section('title', 'Agent Dashboard')

@section('member-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-headset"></i> My Support Dashboard</h2>
    <a href="{{ route('member.tasks.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Ticket
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body text-center">
                <h6>My Tickets</h6>
                <p class="display-6">{{ $stats['total_tasks'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body text-center">
                <h6>Resolved Today</h6>
                <p class="display-6">{{ $agentStats['today_resolved'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info">
            <div class="card-body text-center">
                <h6>In Progress</h6>
                <p class="display-6">{{ $stats['in_progress'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card {{ $agentStats['sla_breaches'] > 0 ? 'text-bg-danger' : 'text-bg-warning' }}">
            <div class="card-body text-center">
                <h6>SLA Breaches</h6>
                <p class="display-6">{{ $agentStats['sla_breaches'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-task"></i> My Tickets
                @if($mySlaTasks->count())
                    <span class="badge bg-danger ms-2">{{ $mySlaTasks->count() }} with SLA</span>
                @endif
            </div>
            <div class="card-body">
                @if($myTasks->count())
                <ul class="list-group list-group-flush">
                    @foreach($myTasks as $task)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div>
                                    <strong>{{ $task->title }}</strong>
                                    @if($task->client)
                                        <small class="text-muted"> — {{ $task->client->name }}</small>
                                    @endif
                                </div>
                                <div class="mt-1">
                                    <span class="badge {{ $task->priority->badgeClass() }}" style="font-size:0.6rem">{{ $task->priority->label() }}</span>
                                    <span class="badge bg-{{ $task->status->value === 'done' ? 'success' : ($task->status->value === 'blocked' ? 'danger' : 'secondary') }}" style="font-size:0.6rem">{{ $task->status->label() }}</span>
                                    @if($task->slaDeadline())
                                        <span class="badge bg-{{ $task->isSlaBreached() ? 'danger' : 'info' }}" style="font-size:0.6rem">
                                            <i class="bi bi-clock"></i>
                                            @if($task->first_responded_at)
                                                Responded
                                            @elseif($task->isSlaBreached())
                                                SLA BREACHED
                                            @else
                                                SLA: {{ $task->slaDeadline()->diffForHumans() }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if($task->due_date)
                                <span class="badge bg-{{ $task->isOverdue() ? 'danger' : ($task->isUrgent() ? 'warning text-dark' : 'secondary') }}" style="font-size:0.6rem">
                                    {{ $task->due_date->format('M d') }}
                                </span>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted mb-0">No tickets assigned.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header text-bg-info">
                <i class="bi bi-person"></i> My Performance
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h3>{{ $agentStats['avg_response_mins'] ?? 'N/A' }} <small class="text-muted" style="font-size:0.5em;">min</small></h3>
                    <small class="text-muted">Avg Response Time</small>
                </div>
                <div class="d-flex justify-content-around text-center">
                    <div>
                        <h4>{{ $agentStats['avg_rating'] ?? '-' }}</h4>
                        <small class="text-muted">Rating</small>
                    </div>
                    <div>
                        <h4>{{ $agentStats['sla_breaches'] }}</h4>
                        <small class="text-muted">SLA Breaches</small>
                    </div>
                    <div>
                        <h4>{{ $stats['done_tasks'] }}</h4>
                        <small class="text-muted">Resolved</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <i class="bi bi-inbox"></i> Unclaimed Tickets
                <span class="badge bg-secondary ms-1">{{ $unclaimedTasks->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($unclaimedTasks->count())
                <ul class="list-group list-group-flush">
                    @foreach($unclaimedTasks as $task)
                    <li class="list-group-item py-2">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span>{{ $task->title }}</span>
                                <small class="text-muted d-block">{{ $task->project?->name }}</small>
                            </div>
                            <form method="POST" action="{{ route('member.tasks.claim', $task) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Claim</button>
                            </form>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted p-3 mb-0">No unclaimed tickets.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
