@extends('layouts.admin')

@section('title', 'Users')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Users</h2>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Create User</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="department_id" class="form-select">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="team_member" {{ request('role') === 'team_member' ? 'selected' : '' }}>Team Member</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($users->count())
        <table class="table table-striped">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge bg-{{ $user->role->value === 'admin' ? 'danger' : ($user->role->value === 'manager' ? 'warning' : 'info') }}">{{ $user->role->label() }}</span></td>
                    <td>{{ $user->department?->name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Delete user {{ $user->name }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $users->links() }}
        @else
        <p class="text-muted mb-0">No users found.</p>
        @endif
    </div>
</div>
@endsection
