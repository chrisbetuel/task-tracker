@extends('layouts.member')

@section('title', $task->title)

@section('member-content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>
        {{ $task->title }}
        <span class="badge {{ $task->priority->badgeClass() }} fs-6">{{ $task->priority->label() }} Priority</span>
    </h2>
    <div>
        <a href="{{ route('member.tasks.my-tasks') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

@if($task->isOverdue())
<div class="alert alert-danger d-flex align-items-center">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Overdue!</strong>&nbsp; This task was due {{ $task->due_date->diffForHumans() }}.
</div>
@elseif($task->isUrgent())
<div class="alert alert-warning d-flex align-items-center">
    <i class="bi bi-clock-fill me-2"></i>
    <strong>Due today!</strong>&nbsp; This task is due {{ $task->due_date->format('M d, Y') }}.
</div>
@endif

<div class="card mb-3">
    <div class="card-header"><i class="bi bi-arrow-repeat"></i> Set Status</div>
    <div class="card-body">
        <div class="btn-group" role="group">
            <form method="POST" action="{{ route('member.tasks.status', $task) }}" class="d-inline">
                @csrf
                <input type="hidden" name="status" value="pending_accept">
                <button type="submit" class="btn btn{{ $task->status->value === 'pending_accept' ? '' : '-outline' }}-secondary">Pending</button>
            </form>
            <form method="POST" action="{{ route('member.tasks.status', $task) }}" class="d-inline">
                @csrf
                <input type="hidden" name="status" value="in_progress">
                <button type="submit" class="btn btn{{ $task->status->value === 'in_progress' ? '' : '-outline' }}-primary">On Going</button>
            </form>
            <form method="POST" action="{{ route('member.tasks.status', $task) }}" class="d-inline">
                @csrf
                <input type="hidden" name="status" value="done">
                <button type="submit" class="btn btn{{ $task->status->value === 'done' ? '' : '-outline' }}-success">Completed</button>
            </form>
        </div>
        <span class="badge bg-{{ $task->status->value === 'done' ? 'success' : ($task->status->value === 'blocked' ? 'danger' : ($task->status->value === 'in_progress' ? 'primary' : 'secondary')) }} fs-6 ms-2">
            {{ $task->status->label() }}
        </span>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-info-circle"></i> Details</span>
                <span class="badge bg-{{ $task->status->value === 'done' ? 'success' : ($task->status->value === 'blocked' ? 'danger' : ($task->status->value === 'in_progress' ? 'primary' : 'secondary')) }}">
                    {{ $task->status->label() }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Project</small>
                        <strong>{{ $task->project->name }}</strong>
                        @php $taskUrl = $task->effectiveProjectUrl(); @endphp
                        @if($taskUrl)
                        <small><a href="{{ $taskUrl }}" target="_blank" class="d-block text-truncate" style="max-width:200px">
                            <i class="bi bi-box-arrow-up-right"></i> {{ $taskUrl }}
                        </a></small>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Assignee</small>
                        <strong>{{ $task->assignee?->name ?? 'Unassigned' }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Created By</small>
                        <strong>{{ $task->creator->name }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Parent Task</small>
                        @if($task->parent)
                        <a href="{{ route('member.tasks.show', $task->parent) }}">{{ $task->parent->title }}</a>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Due Date</small>
                        @if($task->due_date)
                        <strong class="{{ $task->isOverdue() ? 'text-danger' : '' }}">
                            {{ $task->due_date->format('M d, Y') }}
                            @if($task->isOverdue())<i class="bi bi-exclamation-circle text-danger"></i>@endif
                        </strong>
                        @else
                        <span class="text-muted">Not set</span>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Estimated Effort</small>
                        <strong>{{ $task->estimated_minutes ? round($task->estimated_minutes / 60, 1) . ' hours' : 'Not estimated' }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Time Logged</small>
                        <strong>{{ $task->totalTimeLogged() > 0 ? round($task->totalTimeLogged() / 60, 1) . ' hours' : 'None' }}</strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Created</small>
                        <strong>{{ $task->created_at->format('M d, Y H:i') }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Last Updated</small>
                        <strong>{{ $task->updated_at->format('M d, Y H:i') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        @if($task->description)
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-card-text"></i> Description</div>
            <div class="card-body">
                <p class="mb-0" style="white-space: pre-wrap;">{{ $task->description }}</p>
            </div>
        </div>
        @endif

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-diagram-2"></i> Sub-Tasks ({{ $task->children->count() }})</span>
                <a href="{{ route('member.tasks.create') }}?parent_task_id={{ $task->id }}&project_id={{ $task->project_id }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Add Sub-Task
                </a>
            </div>
            <div class="card-body p-0">
                @if($task->children->count())
                <table class="table table-sm mb-0">
                    <thead>
                        <tr><th>Title</th><th>Status</th><th>Priority</th><th>Assignee</th><th>Due</th></tr>
                    </thead>
                    <tbody>
                        @foreach($task->children as $child)
                        <tr class="{{ $child->isOverdue() ? 'table-danger' : '' }}">
                            <td><a href="{{ route('member.tasks.show', $child) }}">{{ $child->title }}</a></td>
                            <td><span class="badge bg-{{ $child->status->value === 'done' ? 'success' : ($child->status->value === 'blocked' ? 'danger' : ($child->status->value === 'in_progress' ? 'primary' : 'secondary')) }}">{{ $child->status->label() }}</span></td>
                            <td><span class="badge {{ $child->priority->badgeClass() }}" style="font-size:0.65rem">{{ $child->priority->label() }}</span></td>
                            <td>{{ $child->assignee?->name ?? '—' }}</td>
                            <td>{{ $child->due_date?->format('M d') ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-muted p-3 mb-0">No sub-tasks yet.</p>
                @endif
            </div>
        </div>

        @if($task->activeBlockages->count())
        <div class="card mb-3 border-danger">
            <div class="card-header text-bg-danger">
                <i class="bi bi-exclamation-triangle"></i> Active Blockages ({{ $task->activeBlockages->count() }})
            </div>
            <div class="card-body">
                @foreach($task->activeBlockages as $blockage)
                <div class="border-start border-danger border-3 ps-3 mb-3">
                    <p class="mb-1"><strong>{{ $blockage->reporter->name }}</strong> <small class="text-muted">{{ $blockage->created_at->diffForHumans() }}</small></p>
                    <p class="mb-0">{{ $blockage->reason }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($task->timeLogs->count())
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> Time Logs
                <span class="badge bg-info ms-2">{{ $task->totalTimeLogged() }} min total</span>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr><th>User</th><th>Minutes</th><th>Date</th><th>Description</th></tr>
                    </thead>
                    <tbody>
                        @foreach($task->timeLogs as $log)
                        <tr>
                            <td>{{ $log->user->name }}</td>
                            <td>{{ $log->minutes }}</td>
                            <td>{{ $log->logged_date->format('M d, Y') }}</td>
                            <td>{{ $log->description ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-arrow-left-right"></i> Assignment History</div>
            <div class="card-body p-0">
                @if($task->assignments->count())
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr><th>User</th><th>Status</th><th>Assigned By</th><th>Date</th><th>Rejection Reason</th></tr>
                        </thead>
                        <tbody>
                            @foreach($task->assignments as $assignment)
                            <tr>
                                <td>{{ $assignment->user->name }}</td>
                                <td><span class="badge bg-{{ $assignment->status === 'rejected' ? 'danger' : ($assignment->status === 'accepted' ? 'success' : 'secondary') }}">{{ ucfirst($assignment->status) }}</span></td>
                                <td>{{ $assignment->assigner?->name ?? 'Self (claimed)' }}</td>
                                <td>{{ $assignment->assigned_at->format('M d, Y H:i') }}</td>
                                <td>{{ $assignment->rejection_reason ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted p-3 mb-0">No assignment history.</p>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-images"></i> Pictures</div>
            <div class="card-body">
                <form method="POST" action="{{ route('assets.store-image', $task) }}" enctype="multipart/form-data" class="mb-3">
                    @csrf
                    <div class="input-group">
                        <input type="file" name="file" class="form-control form-control-sm" accept="image/*" required>
                        <button type="submit" class="btn btn-sm btn-primary">Upload Picture</button>
                    </div>
                    <small class="text-muted">Allowed: JPG, PNG, GIF, WebP (max 20MB)</small>
                </form>

                @php $images = $task->assets->filter(fn($a) => $a->type === 'image'); @endphp
                @if($images->count())
                <div class="row g-2">
                    @foreach($images as $asset)
                    <div class="col-md-4 col-6">
                        <div class="position-relative border rounded p-1 text-center" style="height:140px; overflow:hidden;">
                            <a href="{{ asset('storage/' . $asset->file_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $asset->file_path) }}" class="img-fluid" style="max-height:100px" alt="{{ $asset->name }}">
                            </a>
                            <div class="mt-1 d-flex justify-content-between align-items-center">
                                <small class="text-muted text-truncate d-inline-block" style="max-width:100px">{{ $asset->name }}</small>
                                <form method="POST" action="{{ route('assets.destroy', $asset) }}" class="d-inline" onsubmit="return confirm('Delete this picture?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted small mb-0">No pictures uploaded yet.</p>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-film"></i> Videos</div>
            <div class="card-body">
                <form method="POST" action="{{ route('assets.store-video', $task) }}" enctype="multipart/form-data" class="mb-3">
                    @csrf
                    <div class="input-group">
                        <input type="file" name="file" class="form-control form-control-sm" accept="video/*" required>
                        <button type="submit" class="btn btn-sm btn-primary">Upload Video</button>
                    </div>
                    <small class="text-muted">Allowed: MP4, MOV, AVI, MKV, WebM (up to 2GB)</small>
                </form>

                @php $videos = $task->assets->filter(fn($a) => $a->type === 'video'); @endphp
                @if($videos->count())
                <div class="row g-2">
                    @foreach($videos as $asset)
                    <div class="col-md-4 col-6">
                        <div class="position-relative border rounded p-1 text-center" style="height:140px; overflow:hidden;">
                            <video src="{{ asset('storage/' . $asset->file_path) }}" class="img-fluid" style="max-height:100px" controls></video>
                            <div class="mt-1 d-flex justify-content-between align-items-center">
                                <small class="text-muted text-truncate d-inline-block" style="max-width:100px">{{ $asset->name }}</small>
                                <form method="POST" action="{{ route('assets.destroy', $asset) }}" class="d-inline" onsubmit="return confirm('Delete this video?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted small mb-0">No videos uploaded yet.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-gear"></i> Actions</div>
            <div class="card-body">
                <a href="{{ route('member.tasks.edit', $task) }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                    <i class="bi bi-pencil"></i> Edit Task
                </a>
                @if($task->status->value === 'pending_accept')
                <form method="POST" action="{{ route('member.tasks.accept', $task) }}" class="d-inline w-100 mb-2">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm w-100 mb-2">Accept</button>
                </form>
                <button type="button" class="btn btn-danger btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">Reject</button>
                @endif
                @if(in_array($task->status->value, ['accepted', 'in_progress']))
                <a href="{{ route('member.time-logs.create', $task) }}" class="btn btn-info btn-sm w-100 mb-2">Log Time</a>
                @endif
                @if($task->status->value === 'in_progress' && $task->hasTimeLogged())
                <form method="POST" action="{{ route('member.tasks.done', $task) }}" class="d-inline w-100">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm w-100 mb-2">Mark Done</button>
                </form>
                @endif
                @if(in_array($task->status->value, ['accepted', 'in_progress']))
                <button type="button" class="btn btn-warning btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#blockageModal">Report Blockage</button>
                @endif
                @if($task->status->value === 'blocked')
                <form method="POST" action="{{ route('member.tasks.resolve-blockage', $task) }}" class="d-inline w-100">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm w-100 mb-2">Resolve Blockage</button>
                </form>
                @endif
                @if($task->status->value === 'done')
                <form method="POST" action="{{ route('member.tasks.reopen', $task) }}" class="d-inline w-100">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm w-100 mb-2">Reopen</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

@if($task->status->value === 'pending_accept')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('member.tasks.reject', $task) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for rejection</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if(in_array($task->status->value, ['accepted', 'in_progress']))
<div class="modal fade" id="blockageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('member.tasks.blockage', $task) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Report Blockage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Describe the blockage</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
