@extends('layouts.admin')

@section('title', 'Edit User')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Edit User: {{ $user->name }}</h2>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select @error('role') is-invalid @enderror" name="role" required>
                    <option value="admin" {{ $user->role->value === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="manager" {{ $user->role->value === 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="team_member" {{ $user->role->value === 'team_member' ? 'selected' : '' }}>Team Member</option>
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="department_id" class="form-label">Department</label>
                <select class="form-select @error('department_id') is-invalid @enderror" name="department_id">
                    <option value="">None</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password (leave empty to keep current)</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
