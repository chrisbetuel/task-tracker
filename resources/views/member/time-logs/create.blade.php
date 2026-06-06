@extends('layouts.member')

@section('title', 'Log Time')

@section('member-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Log Time for: {{ $task->title }}</h2>
    <a href="{{ route('member.tasks.my-tasks') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('member.time-logs.store') }}">
            @csrf
            <input type="hidden" name="task_id" value="{{ $task->id }}">
            <div class="mb-3">
                <label for="minutes" class="form-label">Minutes Spent</label>
                <input type="number" class="form-control @error('minutes') is-invalid @enderror" name="minutes" min="1" max="1440" value="{{ old('minutes') }}" required>
                @error('minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="logged_date" class="form-label">Date</label>
                <input type="date" class="form-control @error('logged_date') is-invalid @enderror" name="logged_date" value="{{ old('logged_date', date('Y-m-d')) }}" required>
                @error('logged_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description (optional)</label>
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Log Time</button>
        </form>
    </div>
</div>
@endsection
