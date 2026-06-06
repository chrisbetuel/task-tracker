@extends('layouts.member')

@section('title', 'My Tasks')

@section('member-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">My Tasks</h2>
    <a href="{{ route('member.tasks.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Task
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($tasks->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:200px">Title</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Due</th>
                        <th>Estimate</th>
                        <th>Logged</th>
                        <th>Project</th>
                        <th>Link</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr class="{{ $task->isOverdue() ? 'table-danger' : ($task->isUrgent() ? 'table-warning' : '') }}">
                        <td>
                            <a href="{{ route('member.tasks.show', $task) }}" class="fw-semibold text-decoration-none">{{ $task->title }}</a>
                            @if($task->activeBlockages->count())
                            <i class="bi bi-exclamation-triangle text-danger ms-1" title="Blocked"></i>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $task->priority->badgeClass() }}">{{ $task->priority->label() }}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $task->status->value === 'done' ? 'success' : ($task->status->value === 'blocked' ? 'danger' : ($task->status->value === 'in_progress' ? 'primary' : 'secondary')) }}">
                                {{ $task->status->label() }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            @if($task->due_date)
                                <span class="{{ $task->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                    {{ $task->due_date->format('M d') }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($task->estimated_minutes)
                                <small>{{ round($task->estimated_minutes / 60, 1) }}h</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td><small>{{ $task->totalTimeLogged() > 0 ? round($task->totalTimeLogged() / 60, 1) . 'h' : '0h' }}</small></td>
                        <td><small>{{ $task->project->name }}</small></td>
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
                        <td class="text-nowrap">
                        <div class="btn-group btn-group-sm me-1" role="group">
                            <form method="POST" action="{{ route('member.tasks.status', $task) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="pending_accept">
                                <button type="submit" class="btn btn-outline-secondary btn-sm" style="font-size:0.65rem">Pending</button>
                            </form>
                            <form method="POST" action="{{ route('member.tasks.status', $task) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="in_progress">
                                <button type="submit" class="btn btn-outline-primary btn-sm" style="font-size:0.65rem">To Do</button>
                            </form>
                            <form method="POST" action="{{ route('member.tasks.status', $task) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="done">
                                <button type="submit" class="btn btn-outline-success btn-sm" style="font-size:0.65rem">Done</button>
                            </form>
                        </div>

                        @if($task->status->value === 'pending_accept')
                        <form method="POST" action="{{ route('member.tasks.accept', $task) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Accept</button>
                        </form>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $task->id }}">Reject</button>
                        @endif

                        @if($task->status->value === 'accepted' || $task->status->value === 'in_progress')
                        <form method="POST" action="{{ route('member.tasks.start', $task) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">Start</button>
                        </form>
                        @endif

                        @if(in_array($task->status->value, ['accepted', 'in_progress']))
                        <a href="{{ route('member.time-logs.create', $task) }}" class="btn btn-sm btn-info">Log Time</a>
                        @endif

                        @if($task->status->value === 'in_progress' && $task->hasTimeLogged())
                        <form method="POST" action="{{ route('member.tasks.done', $task) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Mark Done</button>
                        </form>
                        @endif

                        @if(in_array($task->status->value, ['accepted', 'in_progress']))
                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#blockageModal-{{ $task->id }}">Report Blockage</button>
                        @endif

                        @if($task->status->value === 'blocked')
                        <form method="POST" action="{{ route('member.tasks.resolve-blockage', $task) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Resolve Blockage</button>
                        </form>
                        @endif

                        @if(in_array($task->status->value, ['accepted', 'in_progress']))
                        <form method="POST" action="{{ route('member.tasks.unassign', $task) }}" class="d-inline"
                            onsubmit="return confirm('Return this task to unassigned pool?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-secondary">Unassign</button>
                        </form>
                        @endif

                        @if($task->status->value === 'done')
                        <form method="POST" action="{{ route('member.tasks.reopen', $task) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">Reopen</button>
                        </form>
                        @endif
                        <a href="{{ route('member.tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="{{ route('member.tasks.create') }}?parent_task_id={{ $task->id }}&project_id={{ $task->project_id }}" class="btn btn-sm btn-outline-secondary" title="Add Sub-Task">
                            <i class="bi bi-diagram-2"></i>
                        </a>
                    </td>
                </tr>

                @if($task->activeBlockages->count())
                <tr class="table-danger">
                    <td colspan="9">
                        <small><strong>Blocked:</strong> {{ $task->activeBlockages->first()->reason }}</small>
                    </td>
                </tr>
                @endif

                @if($task->status->value === 'pending_accept')
                <div class="modal fade" id="rejectModal-{{ $task->id }}" tabindex="-1">
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
                <div class="modal fade" id="blockageModal-{{ $task->id }}" tabindex="-1">
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
                @endforeach
            </tbody>
        </table>
        {{ $tasks->links() }}
        @else
        <p class="text-muted mb-0">No tasks assigned to you.</p>
        @endif
    </div>
</div>
@endsection
