@extends('layouts.manager')

@section('title', $project->name)

@section('manager-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>
        {{ $project->name }}
        @php $pStatus = $project->computedStatus(); @endphp
        <span class="badge bg-{{ $pStatus === 'accomplished' ? 'success' : ($pStatus === 'in_progress' ? 'primary' : 'secondary') }} fs-6">
            {{ str_replace('_', ' ', ucfirst($pStatus)) }}
        </span>
    </h2>
    <div>
        <a href="{{ route('manager.projects.edit', $project) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('manager.projects.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-info stat-card">
            <div class="card-body text-center">
                <h6>Progress</h6>
                <p class="display-6">{{ $progress['completion_percentage'] }}%</p>
                <small>{{ $progress['done_tasks'] }}/{{ $progress['total_tasks'] }} tasks done</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning stat-card">
            <div class="card-body text-center">
                <h6>Blocked</h6>
                <p class="display-6">{{ $progress['blocked_tasks'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success stat-card">
            <div class="card-body text-center">
                <h6>Time Spent</h6>
                <p class="display-6">{{ $timeSpent['total_hours'] }}h</p>
                <small>across {{ $timeSpent['task_count'] }} tasks</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        @php $pUrl = $project->effectiveUrl(); @endphp
        <div class="card text-bg-dark stat-card">
            <div class="card-body text-center">
                <h6>Project Link</h6>
                @if($pUrl)
                <a href="{{ $pUrl }}" target="_blank" rel="noopener noreferrer" class="text-white">
                    <i class="bi bi-box-arrow-up-right display-6"></i>
                </a>
                <small class="d-block text-truncate mt-1">{{ $pUrl }}</small>
                @else
                <p class="display-6" style="font-size:1.5rem">—</p>
                <small>No URL set</small>
                @endif
            </div>
        </div>
    </div>
</div>

@if($project->description)
<div class="card mb-4">
    <div class="card-body">
        <p class="mb-0" style="white-space: pre-wrap;">{{ $project->description }}</p>
    </div>
</div>
@endif

@if($project->children->count())
<div class="card mb-4">
    <div class="card-header">Sub-Projects</div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @foreach($project->children as $child)
            <li class="list-group-item text-break"><a href="{{ route('manager.projects.show', $child) }}">{{ $child->name }}</a></li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between">
        @php $totalTasks = $project->tasks->count() + $project->tasks->sum(fn($t) => $t->children->count()); @endphp
        <span>Tasks ({{ $totalTasks }})</span>
        <a href="{{ route('manager.tasks.create') }}?project_id={{ $project->id }}" class="btn btn-sm btn-primary">Add Task</a>
    </div>
    <div class="card-body">
        @if($project->tasks->count())
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr><th>Title</th><th>Status</th><th>Assignee</th><th>Blocked</th><th>Media</th></tr>
                </thead>
                <tbody>
                    @foreach($project->tasks as $task)
                    <tr class="table-active">
                        <td class="text-nowrap">
                            <i class="bi bi-diagram-2 me-1 text-muted"></i>
                            <a href="{{ route('manager.tasks.show', $task) }}">{{ $task->title }}</a>
                            <a href="{{ route('manager.tasks.create') }}?parent_task_id={{ $task->id }}&project_id={{ $project->id }}" class="btn btn-sm btn-outline-primary py-0 px-1 ms-1" title="Add Sub-Task">
                                <i class="bi bi-plus-lg"></i>
                            </a>
                            <span class="badge bg-info ms-1">{{ $task->children->count() }} sub</span>
                        </td>
                        <td><span class="badge bg-{{ $task->status->value === 'done' ? 'success' : ($task->status->value === 'blocked' ? 'danger' : ($task->status->value === 'in_progress' ? 'primary' : 'secondary')) }}">{{ $task->status->label() }}</span></td>
                        <td>{{ $task->assignee?->name ?? 'Unassigned' }}</td>
                        <td>{{ $task->activeBlockages->count() ? 'Yes' : 'No' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm mb-1 btn-group-responsive" role="group">
                                <form method="POST" action="{{ route('manager.tasks.status', $task) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="pending_accept">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm" style="font-size:0.6rem">Pending</button>
                                </form>
                                <form method="POST" action="{{ route('manager.tasks.status', $task) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="btn btn-outline-primary btn-sm" style="font-size:0.6rem">To Do</button>
                                </form>
                                <form method="POST" action="{{ route('manager.tasks.status', $task) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="done">
                                    <button type="submit" class="btn btn-outline-success btn-sm" style="font-size:0.6rem">Done</button>
                                </form>
                            </div>
                            @php $taskImages = $task->assets->where('type', 'image'); @endphp
                            @php $taskVideos = $task->assets->where('type', 'video'); @endphp
                            <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                @foreach($taskImages as $asset)
                                <a href="{{ asset('storage/' . $asset->file_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $asset->file_path) }}" class="border rounded" style="width:28px;height:28px;object-fit:cover;" alt="{{ $asset->name }}">
                                </a>
                                @endforeach
                                @foreach($taskVideos as $asset)
                                <a href="{{ asset('storage/' . $asset->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-1" title="{{ $asset->name }}">
                                    <i class="bi bi-film"></i>
                                </a>
                                @endforeach
                            </div>
                            <div class="d-flex gap-1 flex-wrap">
                                <form method="POST" action="{{ route('assets.store-image', $task) }}" enctype="multipart/form-data" class="d-inline">
                                    @csrf
                                    <input type="file" name="file" class="d-none" accept="image/*" required onchange="this.form.submit()" id="img-{{ $task->id }}">
                                    <button type="button" class="btn btn-sm btn-outline-info py-0 px-1" title="Upload Picture" onclick="document.getElementById('img-{{ $task->id }}').click()"><i class="bi bi-image"></i></button>
                                </form>
                                <form method="POST" action="{{ route('assets.store-video', $task) }}" enctype="multipart/form-data" class="d-inline">
                                    @csrf
                                    <input type="file" name="file" class="d-none" accept="video/*" required onchange="this.form.submit()" id="vid-{{ $task->id }}">
                                    <button type="button" class="btn btn-sm btn-outline-warning py-0 px-1" title="Upload Video" onclick="document.getElementById('vid-{{ $task->id }}').click()"><i class="bi bi-film"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @foreach($task->children as $child)
                    <tr>
                        <td style="padding-left: 2.5rem;" class="text-nowrap">
                            <i class="bi bi-arrow-return-right me-1 text-muted"></i>
                            <a href="{{ route('manager.tasks.show', $child) }}">{{ $child->title }}</a>
                        </td>
                        <td><span class="badge bg-{{ $child->status->value === 'done' ? 'success' : ($child->status->value === 'blocked' ? 'danger' : ($child->status->value === 'in_progress' ? 'primary' : 'secondary')) }}">{{ $child->status->label() }}</span></td>
                        <td>{{ $child->assignee?->name ?? 'Unassigned' }}</td>
                        <td>{{ $child->activeBlockages->count() ? 'Yes' : 'No' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm mb-1 btn-group-responsive" role="group">
                                <form method="POST" action="{{ route('manager.tasks.status', $child) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="pending_accept">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm" style="font-size:0.6rem">Pending</button>
                                </form>
                                <form method="POST" action="{{ route('manager.tasks.status', $child) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="btn btn-outline-primary btn-sm" style="font-size:0.6rem">To Do</button>
                                </form>
                                <form method="POST" action="{{ route('manager.tasks.status', $child) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="done">
                                    <button type="submit" class="btn btn-outline-success btn-sm" style="font-size:0.6rem">Done</button>
                                </form>
                            </div>
                            @php $childImages = $child->assets->where('type', 'image'); @endphp
                            @php $childVideos = $child->assets->where('type', 'video'); @endphp
                            <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                @foreach($childImages as $asset)
                                <a href="{{ asset('storage/' . $asset->file_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $asset->file_path) }}" class="border rounded" style="width:28px;height:28px;object-fit:cover;" alt="{{ $asset->name }}">
                                </a>
                                @endforeach
                                @foreach($childVideos as $asset)
                                <a href="{{ asset('storage/' . $asset->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-1" title="{{ $asset->name }}">
                                    <i class="bi bi-film"></i>
                                </a>
                                @endforeach
                            </div>
                            <div class="d-flex gap-1 flex-wrap">
                                <form method="POST" action="{{ route('assets.store-image', $child) }}" enctype="multipart/form-data" class="d-inline">
                                    @csrf
                                    <input type="file" name="file" class="d-none" accept="image/*" required onchange="this.form.submit()" id="img-{{ $child->id }}">
                                    <button type="button" class="btn btn-sm btn-outline-info py-0 px-1" title="Upload Picture" onclick="document.getElementById('img-{{ $child->id }}').click()"><i class="bi bi-image"></i></button>
                                </form>
                                <form method="POST" action="{{ route('assets.store-video', $child) }}" enctype="multipart/form-data" class="d-inline">
                                    @csrf
                                    <input type="file" name="file" class="d-none" accept="video/*" required onchange="this.form.submit()" id="vid-{{ $child->id }}">
                                    <button type="button" class="btn btn-sm btn-outline-warning py-0 px-1" title="Upload Video" onclick="document.getElementById('vid-{{ $child->id }}').click()"><i class="bi bi-film"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-4">
            <i class="bi bi-inbox display-5 text-muted"></i>
            <p class="text-muted mt-2 mb-2">No tasks in this project yet.</p>
            @php $pUrl = $project->effectiveUrl(); @endphp
            @if($pUrl)
            <div class="mb-2">
                <a href="{{ $pUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-box-arrow-up-right"></i> Open Project Link
                </a>
            </div>
            <small class="text-muted d-block mb-2">{{ $pUrl }}</small>
            @else
            <small class="text-muted d-block mb-2">No project URL set.</small>
            @endif
            <a href="{{ route('manager.tasks.create') }}?project_id={{ $project->id }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Create First Task
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
