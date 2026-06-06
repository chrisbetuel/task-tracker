@extends('layouts.member')

@section('title', $profile['user']['name'])

@section('member-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>{{ $profile['user']['name'] }}</h2>
    <a href="{{ route('member.teammates.index') }}" class="btn btn-secondary">Back</a>
</div>

<p class="text-muted mb-4">{{ $profile['user']['email'] }}</p>

<div class="card mb-4">
    <div class="card-header">Acceptance Rate</div>
    <div class="card-body">
        <div class="d-flex justify-content-around text-center">
            <div>
                <h3>{{ $profile['acceptance_rate']['acceptance_rate'] }}%</h3>
                <small>Acceptance Rate</small>
            </div>
            <div>
                <h3>{{ $profile['acceptance_rate']['accepted'] }}</h3>
                <small>Accepted</small>
            </div>
            <div>
                <h3>{{ $profile['acceptance_rate']['rejected'] }}</h3>
                <small>Rejected</small>
            </div>
        </div>
    </div>
</div>

@if(count($profile['projects']))
<div class="card mb-4">
    <div class="card-header">Projects & Progress</div>
    <div class="card-body">
        @foreach($profile['projects'] as $proj)
        <div class="mb-4">
            <h5>{{ $proj['project']['name'] }}</h5>
            <div class="row mb-2">
                <div class="col-md-4">
                    <strong>Completion:</strong> {{ $proj['completion_rate'] }}% ({{ $proj['done_tasks'] }}/{{ $proj['total_tasks'] }})
                </div>
                <div class="col-md-4">
                    <strong>Time Logged:</strong> {{ $proj['total_time_hours'] }}h
                </div>
            </div>
            @if(count($proj['task_titles']))
            <div>
                <small class="text-muted">Tasks: {{ $proj['task_titles']->implode(', ') }}</small>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

@if(count($profile['active_blockages']))
<div class="card mb-4 border-danger">
    <div class="card-header text-bg-danger">Active Blockages</div>
    <div class="card-body">
        @foreach($profile['active_blockages'] as $blockage)
        <div class="mb-2">
            <p><strong>{{ $blockage['task_title'] }}:</strong> {{ $blockage['reason'] }}</p>
            <small class="text-muted">Reported {{ \Carbon\Carbon::parse($blockage['reported_at'])->diffForHumans() }}</small>
        </div>
        @endforeach
    </div>
</div>
@endif

@if(count($profile['recent_completions']))
<div class="card mb-4">
    <div class="card-header">Recent Completions (Last 30 Days)</div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @foreach($profile['recent_completions'] as $completion)
            <li class="list-group-item d-flex justify-content-between">
                <span>{{ $completion['title'] }} <small class="text-muted">({{ $completion['project_name'] }})</small></span>
                <small>{{ \Carbon\Carbon::parse($completion['completed_at'])->format('Y-m-d') }}</small>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif

@if(count($profile['weekly_trend']))
<div class="card">
    <div class="card-header">Weekly Progress Trend</div>
    <div class="card-body">
        <div class="d-flex justify-content-around text-center">
            @foreach($profile['weekly_trend'] as $week)
            <div class="p-2">
                <div><strong>{{ round($week['minutes'] / 60, 1) }}h</strong></div>
                <small class="text-muted">{{ $week['week'] }}</small>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
