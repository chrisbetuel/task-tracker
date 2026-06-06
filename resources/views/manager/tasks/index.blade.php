@extends('layouts.manager')

@section('title', 'Tasks')

@section('manager-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Tasks</h2>
    <a href="{{ route('manager.tasks.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Task
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="pending_accept" {{ request('status') === 'pending_accept' ? 'selected' : '' }}>Pending Accept</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                    <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="priority" class="form-select form-select-sm">
                    <option value="">All Priorities</option>
                    @foreach($priorities as $p)
                    <option value="{{ $p->value }}" {{ request('priority') === $p->value ? 'selected' : '' }}>{{ $p->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="project_id" class="form-select form-select-sm">
                    <option value="">All Projects</option>
                    @foreach($projects as $proj)
                    <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="assignee_id" class="form-select form-select-sm">
                    <option value="">All Assignees</option>
                    @foreach($teamMembers as $member)
                    <option value="{{ $member->id }}" {{ request('assignee_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search by title or description..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('manager.tasks.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check form-check-inline mt-1">
                    <input class="form-check-input" type="checkbox" name="overdue" value="1"
                           id="overdueFilter" {{ request('overdue') ? 'checked' : '' }}>
                    <label class="form-check-label" for="overdueFilter">Overdue only</label>
                </div>
            </div>
            <div class="col-md-3 text-end">
                <small class="text-muted">Sort:
                    <select name="sort" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                        <option value="created_at" {{ request('sort', 'created_at') === 'created_at' ? 'selected' : '' }}>Created</option>
                        <option value="priority" {{ request('sort') === 'priority' ? 'selected' : '' }}>Priority</option>
                        <option value="due_date" {{ request('sort') === 'due_date' ? 'selected' : '' }}>Due Date</option>
                        <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>Title</option>
                        <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>Status</option>
                    </select>
                    <select name="dir" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                        <option value="desc" {{ request('dir', 'desc') === 'desc' ? 'selected' : '' }}>Desc</option>
                        <option value="asc" {{ request('dir') === 'asc' ? 'selected' : '' }}>Asc</option>
                    </select>
                </small>
            </div>
        </form>
    </div>
</div>

@if(request()->anyFilled(['status', 'priority', 'project_id', 'assignee_id', 'search']))
<div class="mb-2">
    <span class="text-muted">Active filters:</span>
    @if(request('status'))<span class="badge bg-secondary me-1">Status: {{ str_replace('_', ' ', request('status')) }}</span>@endif
    @if(request('priority'))<span class="badge bg-secondary me-1">Priority: {{ request('priority') }}</span>@endif
    @if(request('project_id'))<span class="badge bg-secondary me-1">Project filtered</span>@endif
    @if(request('assignee_id'))<span class="badge bg-secondary me-1">Assignee filtered</span>@endif
    @if(request('search'))<span class="badge bg-secondary me-1">Search: "{{ request('search') }}"</span>@endif
    @if(request('overdue'))<span class="badge bg-danger me-1">Overdue only</span>@endif
</div>
@endif

<div class="card">
    <div class="card-body p-0">
        @if($tasks->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:250px">Title</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assignee</th>
                        <th>Project</th>
                        <th>Link</th>
                        <th>Due Date</th>
                        <th>Effort</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr class="{{ $task->isOverdue() ? 'table-danger' : ($task->isUrgent() ? 'table-warning' : '') }}">
                        <td>
                            <a href="{{ route('manager.tasks.show', $task) }}" class="fw-semibold text-decoration-none">
                                {{ $task->title }}
                            </a>
                        </td>
                        <td>
                            <span class="badge {{ $task->priority->badgeClass() }}">{{ $task->priority->label() }}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $task->status->value === 'done' ? 'success' : ($task->status->value === 'blocked' ? 'danger' : ($task->status->value === 'in_progress' ? 'primary' : ($task->status->value === 'rejected' ? 'dark' : 'secondary'))) }}">
                                {{ $task->status->label() }}
                            </span>
                        </td>
                        <td>
                            @if($task->assignee)
                            <span class="text-nowrap">{{ $task->assignee->name }}</span>
                            @else
                            <span class="text-muted fst-italic">Unassigned</span>
                            @endif
                        </td>
                        <td><small>{{ $task->project->name }}</small></td>
                        <td>
                            @php $taskUrl = $task->effectiveProjectUrl(); @endphp
                            @if($taskUrl)
                            <a href="{{ $taskUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="{{ $taskUrl }}">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            @else
                            <span class="text-muted">—</span>
                            @endif
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
                        <td><small class="text-muted text-nowrap">{{ $task->created_at->format('M d') }}</small></td>
                        <td class="text-nowrap">
                            <div class="btn-group btn-group-sm me-1" role="group">
                                <form method="POST" action="{{ route('manager.tasks.status', $task) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="pending_accept">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm" style="font-size:0.65rem">Pending</button>
                                </form>
                                <form method="POST" action="{{ route('manager.tasks.status', $task) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="btn btn-outline-primary btn-sm" style="font-size:0.65rem">To Do</button>
                                </form>
                                <form method="POST" action="{{ route('manager.tasks.status', $task) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="done">
                                    <button type="submit" class="btn btn-outline-success btn-sm" style="font-size:0.65rem">Done</button>
                                </form>
                            </div>
                            <a href="{{ route('manager.tasks.show', $task) }}" class="btn btn-sm btn-outline-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('manager.tasks.create') }}?parent_task_id={{ $task->id }}&project_id={{ $task->project_id }}" class="btn btn-sm btn-outline-primary" title="Add Sub-Task">
                                <i class="bi bi-diagram-2"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $tasks->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox display-4 text-muted"></i>
            <p class="text-muted mt-2 mb-0">No tasks found matching your criteria.</p>
            @if(request()->anyFilled(['status', 'priority', 'project_id', 'assignee_id', 'search']))
            <a href="{{ route('manager.tasks.index') }}" class="btn btn-sm btn-outline-secondary mt-2">Clear Filters</a>
            @else
            <a href="{{ route('manager.tasks.create') }}" class="btn btn-primary mt-2">Create First Task</a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
