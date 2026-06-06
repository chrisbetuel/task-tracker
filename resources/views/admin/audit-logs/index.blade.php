@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('admin-content')
<h2 class="mb-3">Audit Logs</h2>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="department_id" class="form-select">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="action" class="form-select">
                    <option value="">All Actions</option>
                    @foreach($actions as $act)
                    <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($act)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="From">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="To">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($logs->count())
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th style="width:130px">Time</th>
                        <th>Performed By</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Department</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td class="text-nowrap"><small>{{ $log->created_at->format('M d, H:i') }}</small></td>
                        <td><span class="fw-semibold">{{ $log->user?->name ?? 'System' }}</span></td>
                        <td>
                            @php
                                $badge = match($log->action) {
                                    'task_assigned' => 'primary',
                                    'task_accepted' => 'success',
                                    'task_rejected' => 'danger',
                                    'task_completed' => 'success',
                                    'task_created' => 'info',
                                    'task_blocked' => 'warning',
                                    'task_unblocked' => 'success',
                                    'task_unassigned' => 'secondary',
                                    'task_reopened' => 'dark',
                                    'task_started' => 'info',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ str_replace('_', ' ', $log->action) }}</span>
                        </td>
                        <td><small>{{ $log->description }}</small></td>
                        <td><small class="text-muted">{{ $log->department?->name ?? 'N/A' }}</small></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $logs->links() }}
        @else
        <p class="text-muted mb-0">No audit logs found.</p>
        @endif
    </div>
</div>
@endsection
