@extends('layouts.manager')

@section('title', 'Assign Task')

@section('manager-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Assign Task: {{ $task->title }}</h2>
    <a href="{{ route('manager.tasks.show', $task) }}" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('manager.tasks.assign', $task) }}">
            @csrf
            <div class="mb-3">
                <label for="user_id" class="form-label">Team Member</label>
                <select class="form-select @error('user_id') is-invalid @enderror" name="user_id" required>
                    <option value="">Select a team member...</option>
                    @foreach($teamMembers as $member)
                    <option value="{{ $member->id }}" {{ old('user_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                    @endforeach
                </select>
                @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Assign</button>
        </form>
    </div>
</div>
@endsection
