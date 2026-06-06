@extends('layouts.manager')

@section('title', 'Manager Dashboard')

@section('manager-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">{{ $department->name }} Dashboard</h2>
    <div>
        <a href="{{ route('manager.tasks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Task
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-bg-primary">
            <div class="card-body">
                <h5 class="card-title">Projects</h5>
                <p class="display-6">{{ $stats['projects_count'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-success">
            <div class="card-body">
                <h5 class="card-title">Tasks</h5>
                <p class="display-6">{{ $stats['tasks_count'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-info">
            <div class="card-body">
                <h5 class="card-title">Team Members</h5>
                <p class="display-6">{{ $stats['team_members_count'] }}</p>
            </div>
        </div>
    </div>
</div>

<h4>Task Status Distribution</h4>
<div class="row mb-4">
    @foreach($stats['status_distribution'] as $status => $data)
    <div class="col-md-2">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="card-title">{{ $data['label'] }}</h6>
                <p class="display-6">{{ $data['count'] }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if(count($stats['overdue_tasks']))
<div class="card mb-4 border-danger">
    <div class="card-header text-bg-danger">Overdue Tasks (14+ days)</div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @foreach($stats['overdue_tasks'] as $task)
            <li class="list-group-item d-flex justify-content-between">
                <span>{{ $task['title'] }} <small class="text-muted">({{ $task['project']['name'] ?? 'N/A' }})</small></span>
                <span class="badge bg-secondary">{{ $task['status'] }}</span>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">Recent Projects</div>
    <div class="card-body">
        @if($recentProjects->count())
        <ul class="list-group list-group-flush">
            @foreach($recentProjects as $project)
            <li class="list-group-item d-flex justify-content-between">
                <a href="{{ route('manager.projects.show', $project) }}">{{ $project->name }}</a>
                <small>{{ $project->tasks_count }} tasks</small>
            </li>
            @endforeach
        </ul>
        @else
        <p class="text-muted mb-0">No projects yet.</p>
        @endif
    </div>
</div>
@endsection
