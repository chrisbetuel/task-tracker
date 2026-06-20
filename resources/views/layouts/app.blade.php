<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Department Task Tracker')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">TaskTracker</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-bell fs-5"></i>
                                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                                @if ($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="width: 320px;">
                                @forelse (auth()->user()->unreadNotifications->take(5) as $notification)
                                    @php $data = $notification->data; @endphp
                                    <li>
                                        <a class="dropdown-item text-wrap" href="{{ route('notifications.index') }}">
                                            <strong>{{ $data['user_name'] ?? 'Someone' }}</strong>
                                            commented on <strong>{{ $data['project_name'] ?? 'a project' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </a>
                                    </li>
                                    @if (!$loop->last)
                                        <li><hr class="dropdown-divider m-0"></li>
                                    @endif
                                @empty
                                    <li><span class="dropdown-item text-muted">No new notifications</span></li>
                                @endforelse
                                @if ($unreadCount > 0)
                                    <li><hr class="dropdown-divider m-0"></li>
                                    <li>
                                        <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-center small text-primary">Mark all as read</button>
                                        </form>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider m-0"></li>
                                <li><a class="dropdown-item text-center small" href="{{ route('notifications.index') }}">View all notifications</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
