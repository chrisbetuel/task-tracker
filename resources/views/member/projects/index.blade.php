@extends('layouts.member')

@section('title', 'Projects')

@section('member-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Projects</h2>
    <a href="{{ route('member.projects.create') }}" class="btn btn-primary">Create Project</a>
</div>

<div class="card">
    <div class="card-body">
        @if($projects->count())
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr><th>Name</th><th>Status</th><th>Link</th><th>Tasks</th><th>Created</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                    <tr class="table-primary">
                        <td class="text-nowrap">
                            <a href="{{ route('member.projects.show', $project) }}">{{ $project->name }}</a>
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
                        <td>
                            @php $pUrl = $project->effectiveUrl(); @endphp
                            @if($pUrl)
                            <a href="{{ $pUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary" title="{{ $pUrl }}">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $project->tasks_count }}</td>
                        <td>{{ $project->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('member.projects.show', $project) }}" class="btn btn-sm btn-info">View</a>
                        </td>
                    </tr>
                    @if($project->children->count())
                        @foreach($project->children as $child)
                        <tr>
                            <td class="ps-5 text-nowrap">
                                <a href="{{ route('member.projects.show', $child) }}">
                                    <i class="bi bi-arrow-return-right text-muted me-1"></i>{{ $child->name }}
                                </a>
                            </td>
                            <td>
                                @php $cStatus = $child->computedStatus(); @endphp
                                <span class="badge bg-{{ $cStatus === 'accomplished' ? 'success' : ($cStatus === 'in_progress' ? 'primary' : 'secondary') }}" style="font-size:0.65rem">
                                    {{ str_replace('_', ' ', ucfirst($cStatus)) }}
                                </span>
                            </td>
                            <td>
                                @php $cUrl = $child->effectiveUrl(); @endphp
                                @if($cUrl)
                                <a href="{{ $cUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary" title="{{ $cUrl }}">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $child->tasks_count }}</td>
                            <td>{{ $child->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('member.projects.show', $child) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $projects->links() }}
        @else
        <p class="text-muted mb-0">No projects found.</p>
        @endif
    </div>
</div>
@endsection
