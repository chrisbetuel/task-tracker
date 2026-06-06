@extends('layouts.member')

@section('title', 'Marketing Dashboard')

@section('member-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-megaphone"></i> My Content Dashboard</h2>
    <a href="{{ route('member.tasks.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Task
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body text-center">
                <h6>My Tasks</h6>
                <p class="display-6">{{ $stats['total_tasks'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body text-center">
                <h6>Done</h6>
                <p class="display-6">{{ $stats['done_tasks'] }}</p>
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
        <div class="card text-bg-warning">
            <div class="card-body text-center">
                <h6>Pending Approval</h6>
                <p class="display-6">{{ $myApprovals->count() }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar-event"></i> Content Calendar — Upcoming Deadlines
            </div>
            <div class="card-body">
                @if(count($calendarTasks))
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Content</th>
                                <th>Due Date</th>
                                <th>Project</th>
                                <th>Priority</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calendarTasks as $item)
                            <tr class="{{ ($item['status'] === 'in_progress' || $item['status'] === 'accepted' || $item['status'] === 'pending_accept') && \Carbon\Carbon::parse($item['start'])->isPast() ? 'table-danger' : (\Carbon\Carbon::parse($item['start'])->isToday() ? 'table-warning' : '') }}">
                                <td>{{ $item['title'] }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($item['start'])->format('M d') }}
                                    @if(\Carbon\Carbon::parse($item['start'])->isPast())
                                        <span class="badge bg-danger ms-1">OVERDUE</span>
                                    @elseif(\Carbon\Carbon::parse($item['start'])->isToday())
                                        <span class="badge bg-warning text-dark ms-1">TODAY</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $item['project'] }}</small></td>
                                <td><span class="badge {{ \App\Enums\TaskPriority::tryFrom($item['priority'])?->badgeClass() ?? 'bg-secondary' }}" style="font-size:0.6rem;">{{ ucfirst($item['priority']) }}</span></td>
                                <td><span class="badge bg-secondary" style="font-size:0.6rem;">{{ str_replace('_', ' ', ucfirst($item['status'])) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted mb-0">No upcoming deadlines.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-check-circle"></i> My Approvals
                <span class="badge bg-warning ms-1">{{ $myApprovals->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($myApprovals->count())
                <ul class="list-group list-group-flush">
                    @foreach($myApprovals as $task)
                    <li class="list-group-item py-2">
                        <div class="d-flex justify-content-between">
                            <span>{{ $task->title }}</span>
                            <span class="badge {{ $task->approval_status->badgeClass() }}" style="font-size:0.6rem;">{{ $task->approval_status->label() }}</span>
                        </div>
                        <small class="text-muted">{{ $task->project?->name }}</small>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted p-3 mb-0">No items pending your approval.</p>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-up"></i> Acceptance Rate
            </div>
            <div class="card-body text-center">
                <h3>{{ $acceptanceRate['acceptance_rate'] }}%</h3>
                <div class="d-flex justify-content-around small">
                    <span>Accepted: {{ $acceptanceRate['accepted'] }}</span>
                    <span>Rejected: {{ $acceptanceRate['rejected'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">My Tasks</div>
            <div class="card-body">
                @if($myTasks->count())
                <ul class="list-group list-group-flush">
                    @foreach($myTasks as $task)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span>{{ $task->title }}</span>
                            <small class="text-muted d-block">{{ $task->project?->name }}</small>
                            @if($task->channel)
                                <span class="badge bg-secondary" style="font-size:0.6rem;">{{ $task->channel->label() }}</span>
                            @endif
                            <span class="badge {{ $task->priority->badgeClass() }}" style="font-size:0.6rem">{{ $task->priority->label() }}</span>
                            @if($task->approval_status)
                                <span class="badge {{ $task->approval_status->badgeClass() }}" style="font-size:0.6rem">{{ $task->approval_status->label() }}</span>
                            @endif
                            @if($task->due_date)
                                <span class="badge bg-{{ $task->isOverdue() ? 'danger' : ($task->isUrgent() ? 'warning text-dark' : 'secondary') }}" style="font-size:0.6rem">
                                    {{ $task->due_date->format('M d') }}
                                </span>
                            @endif
                        </div>
                        <span class="badge bg-{{ $task->status->value === 'done' ? 'success' : ($task->status->value === 'blocked' ? 'danger' : 'secondary') }}">{{ $task->status->label() }}</span>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted mb-0">No tasks assigned.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Available Unassigned Tasks</div>
            <div class="card-body">
                @if($unclaimedTasks->count())
                <ul class="list-group list-group-flush">
                    @foreach($unclaimedTasks as $task)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span>{{ $task->title }}</span>
                            <small class="text-muted d-block">{{ $task->project?->name }}</small>
                            @if($task->channel)
                                <span class="badge bg-secondary" style="font-size:0.6rem;">{{ $task->channel->label() }}</span>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('member.tasks.claim', $task) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Claim</button>
                        </form>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted mb-0">No unassigned tasks available.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
