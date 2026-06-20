@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Notifications</h1>
    @if (auth()->user()->unreadNotifications->count() > 0)
        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">Mark all as read</button>
        </form>
    @endif
</div>

<div class="list-group">
    @forelse ($notifications as $notification)
        @php
            $data = $notification->data;
        @endphp
        <div class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'list-group-item-primary fw-semibold' }}">
            <div class="d-flex w-100 justify-content-between">
                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                @if (!$notification->read_at)
                    <form method="POST" action="{{ route('notifications.mark-as-read', $notification) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-decoration-none">Mark as read</button>
                    </form>
                @endif
            </div>
            @if (isset($data['project_id']))
                @php
                    $user = auth()->user();
                    $projectLink = match (true) {
                        $user->isAdmin() || $user->isHeadOfOperation() => route('admin.projects.show', $data['project_id']),
                        $user->isManager() => route('manager.projects.show', $data['project_id']),
                        default => route('member.projects.show', $data['project_id']),
                    };
                @endphp
                <p class="mb-1">
                    <strong>{{ $data['user_name'] }}</strong> commented on
                    <a href="{{ $projectLink }}">{{ $data['project_name'] }}</a>
                </p>
                <p class="mb-0 text-muted">{{ $data['comment_body'] }}</p>
            @endif
        </div>
    @empty
        <div class="list-group-item text-center text-muted py-5">
            <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
            No notifications yet.
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $notifications->links() }}
</div>
@endsection
