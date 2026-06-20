@extends('layouts.member')

@section('title', 'My Dashboard')

@section('member-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">My Dashboard</h2>
    <a href="{{ route('member.tasks.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Task
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary stat-card">
            <div class="card-body text-center">
                <h6>My Tasks</h6>
                <p class="display-6">{{ $stats['total_tasks'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success stat-card">
            <div class="card-body text-center">
                <h6>Done</h6>
                <p class="display-6">{{ $stats['done_tasks'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info stat-card">
            <div class="card-body text-center">
                <h6>In Progress</h6>
                <p class="display-6">{{ $stats['in_progress'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning stat-card">
            <div class="card-body text-center">
                <h6>Blocked</h6>
                <p class="display-6">{{ $stats['blocked'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Acceptance Rate</div>
    <div class="card-body">
        <div class="d-flex justify-content-around text-center">
            <div>
                <h3>{{ $acceptanceRate['acceptance_rate'] }}%</h3>
                <small class="text-muted">Acceptance Rate</small>
            </div>
            <div>
                <h3>{{ $acceptanceRate['accepted'] }}</h3>
                <small class="text-muted">Accepted</small>
            </div>
            <div>
                <h3>{{ $acceptanceRate['rejected'] }}</h3>
                <small class="text-muted">Rejected</small>
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
                            <small class="text-muted">({{ $task->project->name }})</small>
                            <span class="badge {{ $task->priority->badgeClass() }}" style="font-size:0.6rem">{{ $task->priority->label() }}</span>
                            @if($task->due_date)
                                <span class="badge bg-{{ $task->isOverdue() ? 'danger' : ($task->isUrgent() ? 'warning text-dark' : 'secondary') }}" style="font-size:0.6rem">
                                    {{ $task->due_date->format('M d') }}
                                </span>
                            @endif
                            <div class="mt-1">
                                <small class="text-muted">
                                    <i class="bi bi-person-up"></i>
                                    @if($task->currentAssignment && $task->currentAssignment->assigner)
                                        Assigned by {{ $task->currentAssignment->assigner->name }}
                                    @elseif($task->creator && $task->creator->id !== auth()->id())
                                        Created by {{ $task->creator->name }}
                                    @else
                                        Claimed by you
                                    @endif
                                </small>
                            </div>
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
                    <li class="list-group-item d-flex justify-content-between">
                        <div>
                            <span>{{ $task->title }}</span>
                            <small class="text-muted d-block">{{ $task->project->name }}</small>
                        </div>
                        <div class="d-flex gap-1">
                            <form method="POST" action="{{ route('member.tasks.claim', $task) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Claim</button>
                            </form>
                            @if($teammates->count())
                            <form method="POST" action="{{ route('member.tasks.assign-member', $task) }}" class="d-flex gap-1">
                                @csrf
                                <select name="user_id" class="form-select form-select-sm" style="width:auto" required>
                                    <option value="">Assign</option>
                                    @foreach($teammates as $teammate)
                                        <option value="{{ $teammate->id }}">{{ $teammate->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Assign to teammate">
                                    <i class="bi bi-person-check"></i>
                                </button>
                            </form>
                            @endif
                        </div>
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

@if($teammates->count() < 10)
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-plus"></i> Quick Add Team Member
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('member.teammates.store') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="Name" required>
                        </div>
                        <div class="col-md-4">
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="Email" required>
                        </div>
                        <div class="col-md-3">
                            <input type="password" name="password" class="form-control form-control-sm" placeholder="Password" required>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-sm btn-primary" title="Add">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-people"></i> Team Members ({{ $teammates->count() }})
            </div>
            <div class="card-body">
                @if($teammates->count())
                <div class="list-group list-group-flush">
                    @foreach($teammates as $teammate)
                    <a href="{{ route('member.teammates.show', $teammate) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2">
                        <span>
                            <i class="bi bi-person-circle me-1"></i> {{ $teammate->name }}
                        </span>
                        <small class="text-muted">{{ $teammate->assigned_tasks_count }} tasks</small>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-muted mb-0">No other team members yet. Add one above!</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection
