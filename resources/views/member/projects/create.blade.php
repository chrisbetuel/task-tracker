@extends('layouts.member')

@section('title', 'Create Project')

@section('member-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Create Project</h2>
    <a href="{{ route('member.projects.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('member.projects.store') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="url" class="form-label">Project URL</label>
                <input type="url" class="form-control @error('url') is-invalid @enderror" name="url" value="{{ old('url') }}" placeholder="https://github.com/org/repo or https://example.com/project">
                @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Link to repository, deployment, or review page. Sub-projects and tasks inherit this URL automatically.</small>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select @error('status') is-invalid @enderror" name="status">
                    <option value="">Auto (based on tasks)</option>
                    <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="accomplished" {{ old('status') === 'accomplished' ? 'selected' : '' }}>Accomplished</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Leave empty for auto-calculation based on task progress.</small>
            </div>
            <div class="mb-3">
                <label for="parent_project_id" class="form-label">Parent Project (optional)</label>
                <select class="form-select @error('parent_project_id') is-invalid @enderror" name="parent_project_id">
                    <option value="">None (Top-Level Project)</option>
                    @foreach($projects as $proj)
                    <option value="{{ $proj->id }}" {{ old('parent_project_id') == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                    @endforeach
                </select>
                @error('parent_project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <hr>
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0 fw-semibold">Sub-Projects</label>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSubProject()">
                        <i class="bi bi-plus"></i> Add Sub-Project
                    </button>
                </div>
                <p class="text-muted small mb-3">Add child projects that will be nested under this project.</p>
                <div id="sub-projects-container">
                    @if(old('sub_projects'))
                        @foreach(old('sub_projects') as $i => $sub)
                        <div class="sub-project-row card card-body mb-2 p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong class="text-muted small">Sub-Project #{{ $i + 1 }}</strong>
                                <button type="button" class="btn-close" onclick="this.closest('.sub-project-row').remove()"></button>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="sub_projects[{{ $i }}][name]" class="form-control form-control-sm" placeholder="Name" value="{{ $sub['name'] }}" required>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="sub_projects[{{ $i }}][description]" class="form-control form-control-sm" placeholder="Description (optional)" value="{{ $sub['description'] }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="url" name="sub_projects[{{ $i }}][url]" class="form-control form-control-sm" placeholder="URL (optional)" value="{{ $sub['url'] }}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
                @error('sub_projects')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <hr>
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0 fw-semibold">Initial Tasks</label>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="addInitialTask()">
                        <i class="bi bi-plus"></i> Add Task
                    </button>
                </div>
                <p class="text-muted small mb-3">Create and optionally assign tasks for this project right away.</p>
                <div id="initial-tasks-container">
                    @if(old('initial_tasks'))
                        @foreach(old('initial_tasks') as $i => $t)
                        <div class="initial-task-row card card-body mb-2 p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong class="text-muted small">Task #{{ $i + 1 }}</strong>
                                <button type="button" class="btn-close" onclick="this.closest('.initial-task-row').remove()"></button>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="initial_tasks[{{ $i }}][title]" class="form-control form-control-sm" placeholder="Task title" value="{{ $t['title'] }}" required>
                                </div>
                                <div class="col-md-3">
                                    <select name="initial_tasks[{{ $i }}][assigned_to]" class="form-select form-select-sm">
                                        <option value="">Unassigned</option>
                                        @foreach($teamMembers as $m)
                                        <option value="{{ $m->id }}" {{ ($t['assigned_to'] ?? '') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="initial_tasks[{{ $i }}][priority]" class="form-select form-select-sm" required>
                                        <option value="low" {{ ($t['priority'] ?? '') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ ($t['priority'] ?? '') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ ($t['priority'] ?? '') == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="critical" {{ ($t['priority'] ?? '') == 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="initial_tasks[{{ $i }}][due_date]" class="form-control form-control-sm" value="{{ $t['due_date'] ?? '' }}" min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-1">
                                    <input type="number" name="initial_tasks[{{ $i }}][estimated_minutes]" class="form-control form-control-sm" placeholder="min" value="{{ $t['estimated_minutes'] ?? '' }}" min="1">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
                @error('initial_tasks')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
</div>

<script>
let subProjectIndex = {{ old('sub_projects') ? count(old('sub_projects')) : 0 }};
let initialTaskIndex = {{ old('initial_tasks') ? count(old('initial_tasks')) : 0 }};
const teamMembers = @json($teamMembers);

function addSubProject() {
    const i = subProjectIndex++;
    const container = document.getElementById('sub-projects-container');
    const row = document.createElement('div');
    row.className = 'sub-project-row card card-body mb-2 p-3';
    row.innerHTML = `
        <div class="d-flex justify-content-between align-items-start mb-2">
            <strong class="text-muted small">Sub-Project #${i + 1}</strong>
            <button type="button" class="btn-close" onclick="this.closest('.sub-project-row').remove()"></button>
        </div>
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="sub_projects[${i}][name]" class="form-control form-control-sm" placeholder="Name" required>
            </div>
            <div class="col-md-5">
                <input type="text" name="sub_projects[${i}][description]" class="form-control form-control-sm" placeholder="Description (optional)">
            </div>
            <div class="col-md-3">
                <input type="url" name="sub_projects[${i}][url]" class="form-control form-control-sm" placeholder="URL (optional)">
            </div>
        </div>
    `;
    container.appendChild(row);
}

function addInitialTask() {
    const i = initialTaskIndex++;
    const container = document.getElementById('initial-tasks-container');
    const memberOptions = teamMembers.map(m =>
        `<option value="${m.id}">${m.name}</option>`
    ).join('');
    const row = document.createElement('div');
    row.className = 'initial-task-row card card-body mb-2 p-3';
    row.innerHTML = `
        <div class="d-flex justify-content-between align-items-start mb-2">
            <strong class="text-muted small">Task #${i + 1}</strong>
            <button type="button" class="btn-close" onclick="this.closest('.initial-task-row').remove()"></button>
        </div>
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="initial_tasks[${i}][title]" class="form-control form-control-sm" placeholder="Task title" required>
            </div>
            <div class="col-md-3">
                <select name="initial_tasks[${i}][assigned_to]" class="form-select form-select-sm">
                    <option value="">Unassigned</option>
                    ${memberOptions}
                </select>
            </div>
            <div class="col-md-2">
                <select name="initial_tasks[${i}][priority]" class="form-select form-select-sm" required>
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="critical">Critical</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="initial_tasks[${i}][due_date]" class="form-control form-control-sm" min="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-1">
                <input type="number" name="initial_tasks[${i}][estimated_minutes]" class="form-control form-control-sm" placeholder="min" min="1">
            </div>
        </div>
    `;
    container.appendChild(row);
}
</script>
@endsection
