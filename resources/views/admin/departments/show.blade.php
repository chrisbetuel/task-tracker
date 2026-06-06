@extends('layouts.admin')

@section('title', $department->name)

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>{{ $department->name }}
        <span class="badge bg-{{ $department->type?->value === 'marketing' ? 'warning text-dark' : ($department->type?->value === 'agent' ? 'info' : 'secondary') }}" style="font-size:0.6rem; vertical-align:middle;">
            <i class="bi {{ $department->type?->icon() ?? 'bi-building' }}"></i>
            {{ $department->type?->label() ?? 'General' }}
        </span>
    </h2>
    <div>
        <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <p>{{ $department->description ?? 'No description.' }}</p>
        @if($department->settings)
        <hr>
        <h6>Settings</h6>
        <pre class="mb-0 small">{{ json_encode($department->settings, JSON_PRETTY_PRINT) }}</pre>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Users ({{ $department->users->count() }})</div>
            <div class="card-body">
                @if($department->users->count())
                <ul class="list-group list-group-flush">
                    @foreach($department->users as $user)
                    <li class="list-group-item">{{ $user->name }} <span class="badge bg-secondary">{{ $user->role }}</span></li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted mb-0">No users.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Projects ({{ $department->projects->count() }})</div>
            <div class="card-body">
                @if($department->projects->count())
                <ul class="list-group list-group-flush">
                    @foreach($department->projects as $project)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.projects.show', $project) }}">{{ $project->name }}</a>
                        @php $pStatus = $project->computedStatus(); @endphp
                        <span class="badge bg-{{ $pStatus === 'accomplished' ? 'success' : ($pStatus === 'in_progress' ? 'primary' : 'secondary') }}">
                            {{ str_replace('_', ' ', ucfirst($pStatus)) }}
                        </span>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted mb-0">No projects.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Recent Tasks</div>
    <div class="card-body">
        @if($department->tasks->count())
        <table class="table table-sm">
            <thead>
                <tr><th>Title</th><th>Status</th><th>Assigned To</th><th>Created</th></tr>
            </thead>
            <tbody>
                @foreach($department->tasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td><span class="badge bg-{{ $task->status->value === 'done' ? 'success' : ($task->status->value === 'blocked' ? 'danger' : 'secondary') }}">{{ $task->status->label() }}</span></td>
                    <td>{{ $task->assignee?->name ?? 'Unassigned' }}</td>
                    <td>{{ $task->created_at->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-muted mb-0">No tasks.</p>
        @endif
    </div>
</div>
@endsection
