@extends('layouts.admin')

@section('title', 'Edit Department')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Edit Department: {{ $department->name }}</h2>
    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.departments.update', $department) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $department->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Department Type</label>
                <select class="form-select @error('type') is-invalid @enderror" name="type" id="type-select" required>
                    @foreach($types as $t)
                        <option value="{{ $t->value }}" {{ old('type', $department->type?->value ?? 'general') === $t->value ? 'selected' : '' }}>
                            {{ $t->label() }}
                        </option>
                    @endforeach
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4">{{ old('description', $department->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Department Settings</label>
                <div id="marketing-settings" class="border rounded p-3 bg-light" style="display:{{ $department->isMarketing() ? 'block' : 'none' }};">
                    <div class="mb-2">
                        <label class="form-label">Default Calendar View</label>
                        <select name="settings[default_calendar_view]" class="form-select">
                            <option value="month" {{ ($department->getSetting('default_calendar_view') ?? 'month') === 'month' ? 'selected' : '' }}>Month</option>
                            <option value="week" {{ $department->getSetting('default_calendar_view') === 'week' ? 'selected' : '' }}>Week</option>
                            <option value="day" {{ $department->getSetting('default_calendar_view') === 'day' ? 'selected' : '' }}>Day</option>
                        </select>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="settings[approval_required]" value="1" id="approval-req"
                            {{ $department->getSetting('approval_required', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="approval-req">Require approval workflow</label>
                    </div>
                </div>
                <div id="agent-settings" class="border rounded p-3 bg-light" style="display:{{ $department->isAgent() ? 'block' : 'none' }};">
                    <div class="mb-2">
                        <label class="form-label">SLA Response Target (hours)</label>
                        <input type="number" name="settings[sla_response_hours]" class="form-control" value="{{ $department->getSetting('sla_response_hours', 4) }}" min="1">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">SLA Resolution Target (hours)</label>
                        <input type="number" name="settings[sla_resolution_hours]" class="form-control" value="{{ $department->getSetting('sla_resolution_hours', 24) }}" min="1">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="settings[auto_assign]" value="1" id="auto-assign"
                            {{ $department->getSetting('auto_assign', false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="auto-assign">Auto-assign unclaimed tickets</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('type-select')?.addEventListener('change', function() {
        const val = this.value;
        document.getElementById('marketing-settings').style.display = val === 'marketing' ? 'block' : 'none';
        document.getElementById('agent-settings').style.display = val === 'agent' ? 'block' : 'none';
    });
</script>
@endpush
@endsection
