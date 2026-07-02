{{-- resources/views/layouts/staff.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UniLeave - @yield('title')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/unileave.css') }}">
    
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo">
            Uni<span>Leave</span>
        </div>
        <nav class="nav flex-column">
            <a href="{{ route('staff.dashboard') }}" class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('staff.leave.apply') }}" class="nav-link {{ request()->routeIs('staff.leave.apply') ? 'active' : '' }}">
                <i class="fas fa-calendar-plus"></i> Apply Leave
            </a>
            <a href="{{ route('staff.leave.requests') }}" class="nav-link {{ request()->routeIs('staff.leave.requests*') ? 'active' : '' }}">
                <i class="fas fa-list"></i> My Requests
            </a>
            <a href="{{ route('staff.permission.time') }}" class="nav-link {{ request()->routeIs('staff.permission.time') ? 'active' : '' }}">
                <i class="fas fa-clock"></i> Time Permission
            </a>
            <a href="{{ route('staff.permission.location') }}" class="nav-link {{ request()->routeIs('staff.permission.location') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt"></i> Location Permission
            </a>
            <a href="{{ route('staff.balance') }}" class="nav-link {{ request()->routeIs('staff.balance') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Leave Balance
            </a>
        </nav>
    </div>
    
    <!-- Topbar -->
    <div class="topbar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-sm btn-light d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                    <div class="user-avatar">
                        {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}
                    </div>
                    <span>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-user me-2"></i> Profile
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
    @stack('scripts')
</body>
</html>