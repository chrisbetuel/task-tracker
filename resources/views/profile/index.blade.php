@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">Profile</div>
            <div class="card-body">
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label text-md-end fw-semibold">Name</label>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $user->name }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label text-md-end fw-semibold">Email</label>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label text-md-end fw-semibold">Role</label>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ ucfirst($user->role->value) }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label text-md-end fw-semibold">Department</label>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $user->department?->name ?? '—' }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label text-md-end fw-semibold">Member Since</label>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $user->created_at->format('F j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Change Password</div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
