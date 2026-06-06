@extends('layouts.admin')

@section('title', 'Departments')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Departments</h2>
    <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">Create Department</a>
</div>

<div class="card">
    <div class="card-body">
        @if($departments->count())
        <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Users</th>
                    <th>Projects</th>
                    <th>Tasks</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $dept)
                <tr>
                    <td><a href="{{ route('admin.departments.show', $dept) }}">{{ $dept->name }}</a></td>
                    <td>
                        <span class="badge bg-{{ $dept->type?->value === 'marketing' ? 'warning text-dark' : ($dept->type?->value === 'agent' ? 'info' : 'secondary') }}">
                            <i class="bi {{ $dept->type?->icon() ?? 'bi-building' }}"></i>
                            {{ $dept->type?->label() ?? 'General' }}
                        </span>
                    </td>
                    <td>{{ $dept->users_count }}</td>
                    <td>{{ $dept->projects_count }}</td>
                    <td>{{ $dept->tasks_count }}</td>
                    <td>{{ $dept->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('admin.departments.show', $dept) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('admin.departments.edit', $dept) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" action="{{ route('admin.departments.destroy', $dept) }}" class="d-inline" onsubmit="return confirm('Delete this department? All associated data will be affected.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        {{ $departments->links() }}
        @else
        <p class="text-muted mb-0">No departments found.</p>
        @endif
    </div>
</div>
@endsection
