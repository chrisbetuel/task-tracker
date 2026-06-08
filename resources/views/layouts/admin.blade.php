@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-3">
        <div class="card sidebar-card mb-4">
            <div class="card-header">
                @if(auth()->user()->isAdmin()) Admin Panel @else Head of Operation Panel @endif
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action{{ request()->routeIs('admin.dashboard') ? ' active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('admin.departments.index') }}" class="list-group-item list-group-item-action{{ request()->routeIs('admin.departments.*') ? ' active' : '' }}">
                    <i class="bi bi-building"></i> Departments
                </a>
                <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action{{ request()->routeIs('admin.users.*') ? ' active' : '' }}">
                    <i class="bi bi-people"></i> Users
                </a>
                <a href="{{ route('admin.projects.index') }}" class="list-group-item list-group-item-action{{ request()->routeIs('admin.projects.*') ? ' active' : '' }}">
                    <i class="bi bi-folder"></i> Projects
                </a>
                <a href="{{ route('admin.assets.index') }}" class="list-group-item list-group-item-action{{ request()->routeIs('admin.assets.*') ? ' active' : '' }}">
                    <i class="bi bi-images"></i> Media Assets
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="list-group-item list-group-item-action{{ request()->routeIs('admin.audit-logs.*') ? ' active' : '' }}">
                    <i class="bi bi-journal-text"></i> Audit Logs
                </a>
                <a href="{{ route('tasks.create') }}" class="list-group-item list-group-item-action new-task">
                    <i class="bi bi-plus-circle"></i> + New Task
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        @yield('admin-content')
    </div>
</div>
@endsection
