@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>@if(isset($parentTask) && $parentTask) Add Sub-Task @else Create Task @endif</h2>
        <div>
            @auth
                @php $back = isset($parentTask) && $parentTask ? route('home') : (auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isManager() ? route('manager.tasks.index') : route('member.tasks.my-tasks'))) @endphp
                <a href="{{ $back }}" class="btn btn-secondary">Back</a>
            @endauth
        </div>
    </div>

    @if(isset($parentTask) && $parentTask)
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="alert alert-info d-flex align-items-center mb-3">
                <i class="bi bi-diagram-2 me-2"></i>
                <strong>Parent Task:</strong>&nbsp;
                <a href="{{ route('home') }}" class="alert-link">{{ $parentTask->title }}</a>
                <span class="badge {{ $parentTask->priority->badgeClass() }} ms-2">{{ $parentTask->priority->label() }}</span>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tasks.store') }}">
                        @csrf
                        @if(isset($parentTask) && $parentTask)
                        <input type="hidden" name="parent_task_id" value="{{ $parentTask->id }}">
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Task Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror"
                                           name="title" value="{{ old('title') }}" placeholder="e.g., Implement user authentication" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priority') is-invalid @enderror" name="priority" required>
                                        @foreach($priorities as $p)
                                        <option value="{{ $p->value }}" {{ old('priority', 'medium') === $p->value ? 'selected' : '' }}>{{ $p->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="mt-1">
                                        @foreach($priorities as $p)
                                        <span class="badge {{ $p->badgeClass() }} me-1" style="font-size:0.7rem">{{ $p->label() }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                                    <select class="form-select @error('project_id') is-invalid @enderror" name="project_id" required>
                                        <option value="">— Select Project —</option>
                                        @foreach($projects as $proj)
                                        <option value="{{ $proj->id }}" {{ old('project_id') == $proj->id ? 'selected' : '' }}>
                                            {{ $proj->name }}@if(auth()->user()->isAdmin()) <small>({{ $proj->department->name }})</small>@endif
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                           name="due_date" value="{{ old('due_date') }}" min="{{ date('Y-m-d') }}">
                                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="estimated_minutes" class="form-label">Effort</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('estimated_minutes') is-invalid @enderror"
                                               name="estimated_minutes" value="{{ old('estimated_minutes') }}" min="1" max="525600" placeholder="120">
                                        <span class="input-group-text">min</span>
                                        @error('estimated_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      name="description" rows="5" placeholder="Describe what needs to be done, acceptance criteria, any relevant details...">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="assign_self" value="1"
                                       id="assignSelf" {{ old('assign_self') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="assignSelf">
                                    Assign this task to myself
                                </label>
                                <div class="text-muted small">Leave unchecked to add to the unassigned pool for anyone to claim</div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-plus-circle"></i> Create Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
