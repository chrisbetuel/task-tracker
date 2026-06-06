@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-3">
        <div class="card sidebar-card mb-4">
            <div class="card-header">
                Manager Panel
                @if(auth()->user()->department)
                    <span class="badge bg-{{ auth()->user()->department->type?->value === 'marketing' ? 'warning text-dark' : (auth()->user()->department->type?->value === 'agent' ? 'info' : 'secondary') }}" style="font-size:0.5rem; vertical-align:middle;">
                        {{ auth()->user()->department->type?->label() ?? 'General' }}
                    </span>
                @endif
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('manager.dashboard') }}" class="list-group-item list-group-item-action{{ request()->routeIs('manager.dashboard') ? ' active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                @if(auth()->user()->department?->isMarketing())
                <a href="{{ route('manager.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-megaphone"></i> Campaigns
                </a>
                @endif
                @if(auth()->user()->department?->isAgent())
                <a href="{{ route('manager.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-headset"></i> Queue
                </a>
                <a href="{{ route('manager.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-trophy"></i> Leaderboard
                </a>
                @endif
                <a href="{{ route('manager.projects.index') }}" class="list-group-item list-group-item-action{{ request()->routeIs('manager.projects.*') ? ' active' : '' }}">
                    <i class="bi bi-folder"></i> Projects
                </a>
                @if(!auth()->user()->department?->isAgent())
                <a href="{{ route('manager.tasks.index') }}" class="list-group-item list-group-item-action{{ request()->routeIs('manager.tasks.*') && !request()->routeIs('manager.tasks.create') ? ' active' : '' }}">
                    <i class="bi bi-check2-square"></i> Tasks
                </a>
                @endif
                <a href="{{ route('manager.tasks.create') }}" class="list-group-item list-group-item-action new-task">
                    <i class="bi bi-plus-circle"></i> + New {{ auth()->user()->department?->isAgent() ? 'Ticket' : 'Task' }}
                </a>
                <a href="{{ route('manager.team.index') }}" class="list-group-item list-group-item-action{{ request()->routeIs('manager.team.*') ? ' active' : '' }}">
                    <i class="bi bi-people"></i> Team
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        @yield('manager-content')
    </div>
</div>
@endsection
