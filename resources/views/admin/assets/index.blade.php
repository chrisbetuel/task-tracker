@extends('layouts.admin')

@section('title', 'Media Assets')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-images"></i> Media Assets</h2>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images</option>
                    <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Videos</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="department_id" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    @foreach(\App\Models\Department::all() as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="task_id" class="form-control form-control-sm" placeholder="Task ID" value="{{ request('task_id') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('admin.assets.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($assets->count())
        <div class="row g-3">
            @foreach($assets as $asset)
            <div class="col-md-3 col-6">
                <div class="card h-100">
                    <div class="position-relative media-container">
                        @if($asset->type === 'video')
                        <video src="{{ asset('storage/' . $asset->file_path) }}" class="w-100 h-100" style="object-fit:contain" controls></video>
                        @else
                        <a href="{{ asset('storage/' . $asset->file_path) }}" target="_blank">
                            <img src="{{ asset('storage/' . $asset->file_path) }}" class="w-100 h-100" style="object-fit:contain" alt="{{ $asset->name }}">
                        </a>
                        @endif
                    </div>
                    <div class="card-body p-2">
                        <small class="d-block text-truncate fw-semibold" title="{{ $asset->name }}">{{ $asset->name }}</small>
                        <small class="text-muted d-block">
                            <i class="bi bi-person"></i> {{ $asset->creator->name }}
                        </small>
                        <small class="text-muted d-block">
                            <i class="bi bi-building"></i> {{ $asset->department?->name ?? '—' }}
                        </small>
                        @if($asset->task)
                        <small class="text-muted d-block text-truncate">
                            <i class="bi bi-check2-square"></i> {{ $asset->task->title }}
                        </small>
                        @endif
                        <small class="text-muted d-block">{{ $asset->created_at->diffForHumans() }}</small>
                        <span class="badge bg-{{ $asset->type === 'video' ? 'warning text-dark' : 'info' }} mt-1" style="font-size:0.55rem">{{ $asset->type }}</span>
                    </div>
                    <div class="card-footer p-1 text-end">
                        <form method="POST" action="{{ route('assets.destroy', $asset) }}" class="d-inline" onsubmit="return confirm('Delete this {{ $asset->type }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-3">
            {{ $assets->links() }}
        </div>
        @else
        <p class="text-muted mb-0 text-center py-4">No media assets found.</p>
        @endif
    </div>
</div>
@endsection
