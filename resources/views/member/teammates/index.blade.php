@extends('layouts.member')

@section('title', 'Teammates')

@section('member-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Teammates</h2>
    <a href="{{ route('member.teammates.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Add Team Member
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($teammates->count())
        <div class="row">
            @foreach($teammates as $teammate)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('member.teammates.show', $teammate) }}">{{ $teammate->name }}</a>
                        </h5>
                        <div class="d-flex justify-content-between">
                            <span>Assigned Tasks: <strong>{{ $teammate->assigned_tasks_count }}</strong></span>
                            <span>Time Entries: <strong>{{ $teammate->time_logs_count }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-muted mb-0">No other team members in your department.</p>
        @endif
    </div>
</div>
@endsection
