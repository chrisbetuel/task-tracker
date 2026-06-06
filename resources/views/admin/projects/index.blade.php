@extends('layouts.admin')

@section('title', 'Projects')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Projects</h2>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <select name="department_id" class="form-select">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search projects..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($projects->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Department</th>
                        <th>Link</th>
                        <th>Tasks</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                    <tr class="table-primary">
                        <td>
                            <a href="{{ route('admin.projects.show', $project) }}" class="fw-semibold text-decoration-none">
                                {{ $project->name }}
                            </a>
                            @if($project->children->count())
                                <span class="badge bg-dark ms-1">{{ $project->children->count() }} sub</span>
                            @endif
                        </td>
                        <td>
                            @php $pStatus = $project->computedStatus(); @endphp
                            <span class="badge bg-{{ $pStatus === 'accomplished' ? 'success' : ($pStatus === 'in_progress' ? 'primary' : 'secondary') }}">
                                {{ str_replace('_', ' ', ucfirst($pStatus)) }}
                            </span>
                        </td>
                        <td><small>{{ $project->department?->name ?? '—' }}</small></td>
                        <td>
                            @php $pUrl = $project->effectiveUrl(); @endphp
                            @if($pUrl)
                            <a href="{{ $pUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="{{ $pUrl }}">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $project->tasks_count }}</td>
                        <td><small class="text-muted">{{ $project->created_at->format('M d, Y') }}</small></td>
                        <td>
                            <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @if($project->children->count())
                        @foreach($project->children as $child)
                        <tr>
                            <td class="ps-5">
                                <a href="{{ route('admin.projects.show', $child) }}" class="text-decoration-none">
                                    <i class="bi bi-arrow-return-right text-muted me-1"></i>{{ $child->name }}
                                </a>
                            </td>
                            <td>
                                @php $cStatus = $child->computedStatus(); @endphp
                                <span class="badge bg-{{ $cStatus === 'accomplished' ? 'success' : ($cStatus === 'in_progress' ? 'primary' : 'secondary') }}" style="font-size:0.65rem">
                                    {{ str_replace('_', ' ', ucfirst($cStatus)) }}
                                </span>
                            </td>
                            <td><small class="text-muted">—</small></td>
                            <td>
                                @php $cUrl = $child->effectiveUrl(); @endphp
                                @if($cUrl)
                                <a href="{{ $cUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="{{ $cUrl }}">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $child->tasks_count }}</td>
                            <td><small class="text-muted">{{ $child->created_at->format('M d, Y') }}</small></td>
                            <td>
                                <a href="{{ route('admin.projects.show', $child) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $projects->links() }}</div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-folder display-4 text-muted"></i>
            <p class="text-muted mt-2 mb-0">No projects found.</p>
        </div>
        @endif
    </div>
</div>
@endsection
