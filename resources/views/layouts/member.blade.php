@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-3">
        <div class="card sidebar-card mb-4">
            <div class="card-header">
                Team Member Panel
                @if(auth()->user()->department)
                    <span class="badge bg-{{ auth()->user()->department->type?->value === 'marketing' ? 'warning text-dark' : (auth()->user()->department->type?->value === 'agent' ? 'info' : 'secondary') }}" style="font-size:0.5rem; vertical-align:middle;">
                        {{ auth()->user()->department->type?->label() ?? 'General' }}
                    </span>
                @endif
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('member.dashboard') }}" class="list-group-item list-group-item-action{{ request()->routeIs('member.dashboard') ? ' active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                @if(auth()->user()->department?->isMarketing())
                <a href="{{ route('member.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-calendar-event"></i> Content Calendar
                </a>
                @endif
                @if(auth()->user()->department?->isAgent())
                <a href="{{ route('member.tasks.my-tasks') }}" class="list-group-item list-group-item-action{{ request()->routeIs('member.tasks.my-tasks') ? ' active' : '' }}">
                    <i class="bi bi-ticket"></i> My Tickets
                </a>
                <a href="{{ route('member.tasks.unassigned') }}" class="list-group-item list-group-item-action{{ request()->routeIs('member.tasks.unassigned') ? ' active' : '' }}">
                    <i class="bi bi-inbox"></i> Unclaimed Tickets
                </a>
                @else
                <a href="{{ route('member.tasks.my-tasks') }}" class="list-group-item list-group-item-action{{ request()->routeIs('member.tasks.my-tasks') ? ' active' : '' }}">
                    <i class="bi bi-check2-square"></i> My Tasks
                </a>
                <a href="{{ route('member.tasks.unassigned') }}" class="list-group-item list-group-item-action{{ request()->routeIs('member.tasks.unassigned') ? ' active' : '' }}">
                    <i class="bi bi-inbox"></i> Unassigned Tasks
                </a>
                @endif
                @if(auth()->user()->department)
                <a href="{{ route('member.projects.index') }}" class="list-group-item list-group-item-action{{ request()->routeIs('member.projects.*') ? ' active' : '' }}">
                    <i class="bi bi-folder"></i> Projects
                </a>
                @endif
                <a href="{{ route('member.tasks.create') }}" class="list-group-item list-group-item-action new-task">
                    <i class="bi bi-plus-circle"></i> + New {{ auth()->user()->department?->isAgent() ? 'Ticket' : 'Task' }}
                </a>
                <a href="{{ route('member.teammates.index') }}" class="list-group-item list-group-item-action{{ request()->routeIs('member.teammates.*') ? ' active' : '' }}">
                    <i class="bi bi-people"></i> Teammates
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        @yield('member-content')
    </div>
</div>
@endsection
