@extends('layouts.member')

@section('title', 'Edit Task')

@section('member-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Edit Task</h2>
    <a href="{{ route('member.tasks.my-tasks') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('member.tasks.update', $task) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label">Task Title</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $task->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="5">{{ old('description', $task->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select @error('priority') is-invalid @enderror" name="priority" required>
                        @foreach($priorities as $p)
                        <option value="{{ $p->value }}" {{ old('priority', $task->priority->value) === $p->value ? 'selected' : '' }}>
                            {{ $p->label() }}
                        </option>
                        @endforeach
                    </select>
                    @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" class="form-control @error('due_date') is-invalid @enderror" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="estimated_minutes" class="form-label">Estimated Effort (minutes)</label>
                    <input type="number" class="form-control @error('estimated_minutes') is-invalid @enderror" name="estimated_minutes" value="{{ old('estimated_minutes', $task->estimated_minutes) }}" min="1" max="525600">
                    @error('estimated_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('member.tasks.my-tasks') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Update Task
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
