@extends('layouts.member')

@section('title', 'Unassigned Tasks')

@section('member-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Unassigned Tasks</h2>
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
                        <th>Due Date</th>
                        <th>Estimate</th>
                        <th>Project</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $task->title }}</span>
                            @if($task->parent_task_id)
                                <span class="badge bg-info text-dark" style="font-size:0.6rem">SUB</span>
                            @endif
                            <small class="text-muted d-block">{{ Str::limit($task->description, 80) }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $task->priority->badgeClass() }}">{{ $task->priority->label() }}</span>
                        </td>
                        <td>
                            @if($task->due_date)
                                {{ $task->due_date->format('M d, Y') }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $task->estimated_minutes ? round($task->estimated_minutes / 60, 1) . 'h' : '—' }}</td>
                        <td><small>{{ $task->project->name }}</small></td>
                        <td><small class="text-muted">{{ $task->created_at->format('M d') }}</small></td>
                        <td>
                            <div class="d-flex gap-1">
                                <form method="POST" action="{{ route('member.tasks.claim', $task) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Claim for yourself">
                                        <i class="bi bi-hand-index"></i> Claim
                                    </button>
                                </form>
                                @if($teammates->count())
                                <form method="POST" action="{{ route('member.tasks.assign-member', $task) }}" class="d-flex gap-1">
                                    @csrf
                                    <select name="user_id" class="form-select form-select-sm" style="width:auto" required>
                                        <option value="">Assign to...</option>
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
            <p class="text-muted mt-2 mb-0">No unassigned tasks available.</p>
        </div>
        @endif
    </div>
</div>
@endsection
