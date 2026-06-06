@extends('layouts.admin')

@section('title', $project->name)

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>
        {{ $project->name }}
        @php $pStatus = $project->computedStatus(); @endphp
        <span class="badge bg-{{ $pStatus === 'accomplished' ? 'success' : ($pStatus === 'in_progress' ? 'primary' : 'secondary') }} fs-6">
            {{ str_replace('_', ' ', ucfirst($pStatus)) }}
        </span>
    </h2>
    <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">Back to Projects</a>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-info">
            <div class="card-body text-center">
                <h6>Progress</h6>
                <p class="display-6">{{ $progress['completion_percentage'] }}%</p>
                <small>{{ $progress['done_tasks'] }}/{{ $progress['total_tasks'] }} tasks</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body text-center">
                <h6>Blocked</h6>
                <p class="display-6">{{ $progress['blocked_tasks'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body text-center">
                <h6>Time Logged</h6>
                <p class="display-6">{{ $timeSpent['total_hours'] }}h</p>
                <small>{{ $timeSpent['task_count'] }} tasks with time</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        @php $pUrl = $project->effectiveUrl(); @endphp
        <div class="card text-bg-dark">
            <div class="card-body text-center">
                <h6>Project Link</h6>
                @if($pUrl)
                <a href="{{ $pUrl }}" target="_blank" rel="noopener noreferrer" class="text-white">
                    <i class="bi bi-box-arrow-up-right display-6"></i>
                </a>
                <small class="d-block text-truncate mt-1">{{ $pUrl }}</small>
                @else
                <p class="display-6" style="font-size:1.5rem">—</p>
                <small>No URL set</small>
                @endif
            </div>
        </div>
    </div>
</div>

@if($project->description)
<div class="card mb-4">
    <div class="card-header">Description</div>
    <div class="card-body">
        <p class="mb-0" style="white-space:pre-wrap">{{ $project->description }}</p>
    </div>
</div>
@endif

@if($project->children->count())
<div class="card mb-4">
    <div class="card-header">Sub-Projects ({{ $project->children->count() }})</div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead>
                <tr><th>Name</th><th>Department</th><th>Tasks</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($project->children as $child)
                <tr>
                    <td><a href="{{ route('admin.projects.show', $child) }}">{{ $child->name }}</a></td>
                    <td><small>{{ $child->department?->name }}</small></td>
                    <td>{{ $child->tasks->count() }}</td>
                    <td><a href="{{ route('admin.projects.show', $child) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($memberStats->count())
<div class="card mb-4">
    <div class="card-header">Team Members Working on This Project</div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Tasks Assigned</th>
                    <th>Done</th>
                    <th>Completion</th>
                    <th>Hours Logged</th>
                </tr>
            </thead>
            <tbody>
                @foreach($memberStats as $ms)
                <tr>
                    <td><span class="fw-semibold">{{ $ms['user']->name }}</span></td>
                    <td>{{ $ms['total_tasks'] }}</td>
                    <td>{{ $ms['done_tasks'] }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:6px">
                                <div class="progress-bar bg-{{ $ms['completion_rate'] >= 80 ? 'success' : ($ms['completion_rate'] >= 40 ? 'warning' : 'danger') }}"
                                     style="width:{{ $ms['completion_rate'] }}%">
                                </div>
                            </div>
                            <small>{{ $ms['completion_rate'] }}%</small>
                        </div>
                    </td>
                    <td>{{ $ms['hours_logged'] }}h</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">Tasks ({{ $project->tasks->count() }})</div>
    <div class="card-body p-0">
        @if($project->tasks->count())
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Assignee</th>
                    <th>Link</th>
                    <th>Due</th>
                    <th>Time Logged</th>
                    <th>Media</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->tasks as $task)
                <tr class="{{ $task->isOverdue() ? 'table-danger' : '' }}">
                    <td>
                        <span class="fw-semibold">{{ $task->title }}</span>
                        @if($task->parent_task_id)
                            <span class="badge bg-info text-dark" style="font-size:0.55rem">SUB</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $task->status->value === 'done' ? 'success' : ($task->status->value === 'blocked' ? 'danger' : ($task->status->value === 'in_progress' ? 'primary' : 'secondary')) }}">
                            {{ $task->status->label() }}
                        </span>
                    </td>
                    <td><span class="badge {{ $task->priority->badgeClass() }}" style="font-size:0.6rem">{{ $task->priority->label() }}</span></td>
                    <td><small>{{ $task->assignee?->name ?? '—' }}</small></td>
                    <td>
                        @php $taskUrl = $task->effectiveProjectUrl(); @endphp
                        @if($taskUrl)
                        <a href="{{ $taskUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-1" title="{{ $taskUrl }}">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($task->due_date)
                            <small class="{{ $task->isOverdue() ? 'text-danger fw-bold' : '' }}">{{ $task->due_date->format('M d') }}</small>
                        @else
                            <small class="text-muted">—</small>
                        @endif
                    </td>
                    <td><small>{{ $task->totalTimeLogged() > 0 ? round($task->totalTimeLogged() / 60, 1) . 'h' : '—' }}</small></td>
                    <td>
                        <a href="{{ route('admin.assets.index', ['task_id' => $task->id]) }}" class="btn btn-sm btn-outline-info py-0 px-1" title="View Media">
                            <i class="bi bi-images"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center py-4">
            <i class="bi bi-inbox display-5 text-muted"></i>
            <p class="text-muted mt-2 mb-2">No tasks in this project yet.</p>
            @php $pUrl = $project->effectiveUrl(); @endphp
            @if($pUrl)
            <div class="mb-2">
                <a href="{{ $pUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-box-arrow-up-right"></i> Open Project Link
                </a>
            </div>
            <small class="text-muted d-block mb-2">{{ $pUrl }}</small>
            @else
            <small class="text-muted d-block mb-2">No project URL set.</small>
            @endif
            <a href="{{ route('admin.tasks.create') }}?project_id={{ $project->id }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Create First Task
            </a>
        </div>
        @endif
    </div>
</div>

@if($assets->count())
<div class="card mt-4">
    <div class="card-header"><i class="bi bi-images"></i> Media Assets ({{ $assets->count() }})</div>
    <div class="card-body">
        <div class="row g-2">
            @foreach($assets as $asset)
            <div class="col-md-2 col-4">
                <div class="position-relative border rounded p-1 text-center" style="height:120px; overflow:hidden;">
                    @if($asset->type === 'video')
                    <video src="{{ asset('storage/' . $asset->file_path) }}" class="img-fluid" style="max-height:80px" controls></video>
                    @else
                    <a href="{{ asset('storage/' . $asset->file_path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $asset->file_path) }}" class="img-fluid" style="max-height:80px" alt="{{ $asset->name }}">
                    </a>
                    @endif
                    <div class="mt-1">
                        <small class="text-muted text-truncate d-block" style="font-size:0.6rem">{{ $asset->name }}</small>
                        <small class="text-muted d-block" style="font-size:0.55rem">
                            <i class="bi bi-person"></i> {{ $asset->creator->name }}
                            @if($asset->task)
                            · <i class="bi bi-check2-square"></i> {{ $asset->task->title }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
