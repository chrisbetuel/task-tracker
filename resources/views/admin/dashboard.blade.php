@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('admin-content')
<h2 class="mb-4">Admin Dashboard</h2>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary stat-card">
            <div class="card-body text-center">
                <h6>Departments</h6>
                <p class="display-6">{{ $stats['departments_count'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success stat-card">
            <div class="card-body text-center">
                <h6>Users</h6>
                <p class="display-6">{{ $stats['users_count'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info stat-card">
            <div class="card-body text-center">
                <h6>Projects</h6>
                <p class="display-6">{{ $stats['projects_count'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning stat-card">
            <div class="card-body text-center">
                <h6>Total Tasks</h6>
                <p class="display-6">{{ $stats['tasks_count'] }}</p>
            </div>
        </div>
    </div>
</div>

@if(!empty($departmentTypeCounts))
<div class="row mb-4">
    @foreach($departmentTypeCounts as $type => $count)
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body text-center">
                <h6 class="small">
                    <i class="bi {{ \App\Enums\DepartmentType::tryFrom($type)?->icon() ?? 'bi-building' }}"></i>
                    {{ \App\Enums\DepartmentType::tryFrom($type)?->label() ?? ucfirst($type) }}
                </h6>
                <p class="display-6">{{ $count }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<h5 class="mb-3">Tasks by Status</h5>
<div class="row mb-4">
    @foreach($stats['tasks_by_status'] as $status => $count)
    <div class="col-md-2 col-4">
        <div class="card stat-card">
            <div class="card-body text-center">
                <h6 class="card-title text-capitalize small">{{ str_replace('_', ' ', $status) }}</h6>
                <p class="display-6">{{ $count }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

<hr class="my-4">

<div class="d-flex flex-wrap gap-2 mb-4" id="dashboardTabs">
    <button class="btn btn-outline-primary btn-sm active" data-bs-toggle="collapse" data-bs-target="#deptProgress" aria-expanded="true">
        <i class="bi bi-building"></i> Department Progress
    </button>
    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#teamProgress" aria-expanded="false">
        <i class="bi bi-people"></i> Team Member Progress
    </button>
    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#recentProjects" aria-expanded="false">
        <i class="bi bi-folder"></i> Recent Projects
    </button>
    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#recentDepts" aria-expanded="false">
        <i class="bi bi-collection"></i> Recent Departments
    </button>
    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#recentUsers" aria-expanded="false">
        <i class="bi bi-person-badge"></i> Recent Users
    </button>
</div>

<div class="collapse show" id="deptProgress">
    <h5 class="mb-3">Department Progress</h5>
    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Department</th>
                            <th>Projects</th>
                            <th>Total Tasks</th>
                            <th>Done</th>
                            <th>Blocked</th>
                            <th>Completion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departmentProgress as $dp)
                        <tr>
                            <td><a href="{{ route('admin.departments.show', $dp['department']) }}">{{ $dp['department']->name }}</a></td>
                            <td>{{ $dp['department']->projects_count }}</td>
                            <td>{{ $dp['total_tasks'] }}</td>
                            <td>{{ $dp['done_tasks'] }}</td>
                            <td>{{ $dp['blocked_tasks'] }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:8px">
                                        <div class="progress-bar bg-{{ $dp['completion_rate'] >= 80 ? 'success' : ($dp['completion_rate'] >= 40 ? 'warning' : 'danger') }}"
                                             style="width:{{ $dp['completion_rate'] }}%">
                                        </div>
                                    </div>
                                    <small class="fw-semibold">{{ $dp['completion_rate'] }}%</small>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="collapse" id="teamProgress">
    <h5 class="mb-3">Team Member Progress</h5>
    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Tasks</th>
                            <th>Done</th>
                            <th>Time Entries</th>
                            <th>Completion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($memberProgress as $mp)
                        <tr>
                            <td><span class="fw-semibold">{{ $mp['user']->name }}</span></td>
                            <td><small>{{ $mp['user']->department?->name ?? '—' }}</small></td>
                            <td>{{ $mp['total_tasks'] }}</td>
                            <td>{{ $mp['done_tasks'] }}</td>
                            <td>{{ $mp['time_entries'] }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px">
                                        <div class="progress-bar bg-{{ $mp['completion_rate'] >= 80 ? 'success' : ($mp['completion_rate'] >= 40 ? 'warning' : 'danger') }}"
                                             style="width:{{ $mp['completion_rate'] }}%">
                                        </div>
                                    </div>
                                    <small class="fw-semibold">{{ $mp['completion_rate'] }}%</small>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No team members yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="collapse" id="recentProjects">
    <h5 class="mb-3">Recent Projects</h5>
    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Project</th>
                            <th>Dept</th>
                            <th>Tasks</th>
                            <th>Done</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentProjects as $rp)
                        <tr>
                            <td><span class="fw-semibold small">{{ $rp['project']->name }}</span></td>
                            <td><small>{{ $rp['project']->department?->name ?? '—' }}</small></td>
                            <td>{{ $rp['total_tasks'] }}</td>
                            <td>{{ $rp['done_tasks'] }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <div class="progress flex-grow-1" style="height:6px">
                                        <div class="progress-bar bg-{{ $rp['completion_rate'] >= 80 ? 'success' : ($rp['completion_rate'] >= 40 ? 'warning' : 'danger') }}"
                                             style="width:{{ $rp['completion_rate'] }}%">
                                        </div>
                                    </div>
                                    <small>{{ $rp['completion_rate'] }}%</small>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No projects yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="collapse" id="recentDepts">
    <h5 class="mb-3">Recent Departments</h5>
    <div class="card mb-4">
        <div class="card-body">
            @if($recentDepartments->count())
            <ul class="list-group list-group-flush">
                @foreach($recentDepartments as $dept)
                 <li class="list-group-item d-flex justify-content-between align-items-center">
                     <div>
                         <a href="{{ route('admin.departments.show', $dept) }}">{{ $dept->name }}</a>
                         <span class="badge bg-{{ $dept->type?->value === 'marketing' ? 'warning text-dark' : ($dept->type?->value === 'agent' ? 'info' : 'secondary') }}" style="font-size:0.5rem;">
                             {{ $dept->type?->label() ?? 'General' }}
                         </span>
                     </div>
                     <small>{{ $dept->created_at->diffForHumans() }}</small>
                 </li>
                @endforeach
            </ul>
            @else
            <p class="text-muted mb-0">No departments yet.</p>
            @endif
        </div>
    </div>
</div>

<div class="collapse" id="recentUsers">
    <h5 class="mb-3">Recent Users</h5>
    <div class="card mb-4">
        <div class="card-body">
            @if($recentUsers->count())
            <ul class="list-group list-group-flush">
                @foreach($recentUsers as $user)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ $user->name }} <small class="text-muted">({{ $user->department?->name ?? 'N/A' }})</small></span>
                    <small>{{ $user->created_at->diffForHumans() }}</small>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-muted mb-0">No users yet.</p>
            @endif
        </div>
    </div>
</div>

<hr class="my-4">

<h4 class="mb-3">Quick Management</h4>
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-building"></i> Departments</span>
                <a href="{{ route('admin.departments.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> New</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Name</th><th>Type</th><th>Users</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach($recentDepartments as $dept)
                            <tr>
                                <td><a href="{{ route('admin.departments.show', $dept) }}">{{ $dept->name }}</a></td>
                                <td><span class="badge bg-{{ $dept->type?->value === 'marketing' ? 'warning text-dark' : ($dept->type?->value === 'agent' ? 'info' : 'secondary') }}" style="font-size:0.55rem">{{ $dept->type?->label() ?? 'General' }}</span></td>
                                <td>{{ $dept->users_count ?? 0 }}</td>
                                <td>
                                    <a href="{{ route('admin.departments.edit', $dept) }}" class="btn btn-sm btn-warning py-0 px-1"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.departments.destroy', $dept) }}" class="d-inline" onsubmit="return confirm('Delete {{ $dept->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger py-0 px-1"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.departments.index') }}" class="btn btn-sm btn-outline-secondary">Manage All Departments</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-people"></i> Users</span>
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> New</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Name</th><th>Role</th><th>Department</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td><span class="badge bg-{{ $user->role->value === 'admin' ? 'danger' : ($user->role->value === 'manager' ? 'warning' : 'info') }}" style="font-size:0.55rem">{{ $user->role->label() }}</span></td>
                                <td><small>{{ $user->department?->name ?? '—' }}</small></td>
                                <td>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning py-0 px-1"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Delete {{ $user->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger py-0 px-1"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Manage All Users</a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('dashboardTabs').addEventListener('click', function(e) {
    if (e.target.classList.contains('btn')) {
        this.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');
    }
});
</script>
@endsection
