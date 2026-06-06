@extends('layouts.manager')

@section('title', 'Team')

@section('manager-content')
<h2 class="mb-3">Team Members</h2>

<div class="card">
    <div class="card-body">
        @if($teamMembers->count())
        <div class="row">
            @foreach($teamMembers as $member)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $member->name }}</h5>
                        <p class="card-text">
                            <small class="text-muted">{{ $member->email }}</small>
                        </p>
                        <div class="d-flex justify-content-between">
                            <span>Assigned Tasks: <strong>{{ $member->assigned_tasks_count }}</strong></span>
                            <span>Time Logs: <strong>{{ $member->time_logs_count }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-muted mb-0">No team members in this department.</p>
        @endif
    </div>
</div>
@endsection
