@extends('layouts.admin')

@section('title', 'Create Department')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Create Department</h2>
    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.departments.store') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Department Type</label>
                <select class="form-select @error('type') is-invalid @enderror" name="type" id="type-select" required>
                    <option value="">Select type...</option>
                    @foreach($types as $t)
                        <option value="{{ $t->value }}" {{ old('type') === $t->value ? 'selected' : '' }}
                            data-icon="{{ $t->icon() }}">
                            {{ $t->label() }}
                        </option>
                    @endforeach
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text mt-2" id="type-help">
                    <span class="badge bg-secondary">General</span> Standard task tracking for any team.
                    <span class="badge bg-warning text-dark">Marketing</span> Campaigns, content calendar, approval workflow, channel filtering.
                    <span class="badge bg-info">Agent</span> Ticket queue, SLA tracking, client history, team leaderboard.
                </div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3" id="settings-fields" style="display:none;">
                <label class="form-label">Department Settings</label>
                <div id="marketing-settings" class="border rounded p-3 bg-light" style="display:none;">
                    <p class="text-muted small">Marketing-specific settings will be configured after creation.</p>
                </div>
                <div id="agent-settings" class="border rounded p-3 bg-light" style="display:none;">
                    <p class="text-muted small">Agent-specific settings will be configured after creation.</p>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('type-select')?.addEventListener('change', function() {
        const val = this.value;
        document.getElementById('marketing-settings').style.display = val === 'marketing' ? 'block' : 'none';
        document.getElementById('agent-settings').style.display = val === 'agent' ? 'block' : 'none';
        document.getElementById('settings-fields').style.display = (val === 'marketing' || val === 'agent') ? 'block' : 'none';
    });
</script>
@endpush
@endsection
