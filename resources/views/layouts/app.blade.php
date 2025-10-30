<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Admin Panel') }} - @yield('title')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            min-height: 100vh;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 0.75rem 1rem;
            margin-bottom: 0.25rem;
            border-radius: 0.25rem;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #495057;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <aside class="col-md-3 col-lg-2 d-md-block sidebar p-3">
                <div class="mb-4">
                    <h1 class="h4 text-white">Admin Panel</h1>
                    <p class="small text-muted" id="username-display"></p>
                </div>
                <nav class="nav flex-column">
                    <a href="/admin/dashboard" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a href="/admin/apps" class="nav-link {{ request()->is('admin/apps*') ? 'active' : '' }}">
                        <i class="bi bi-app-indicator me-2"></i> Apps
                    </a>
                    <a href="/admin/admob-accounts" class="nav-link {{ request()->is('admin/admob-accounts*') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack me-2"></i> AdMob Accounts
                    </a>
                    <a href="/admin/notifications" class="nav-link {{ request()->is('admin/notifications*') ? 'active' : '' }}">
                        <i class="bi bi-bell me-2"></i> Notifications
                    </a>
                    <a href="/admin/analytics" class="nav-link {{ request()->is('admin/analytics*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up me-2"></i> Analytics
                    </a>
                    <a href="/admin/settings" class="nav-link {{ request()->is('admin/settings*') ? 'active' : '' }}">
                        <i class="bi bi-gear me-2"></i> Settings
                    </a>
                </nav>
                <div class="mt-auto pt-4">
                    <button onclick="handleLogout()" class="btn btn-danger w-100">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </div>
            </aside>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Add auth token to all API requests
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            const token = localStorage.getItem('auth_token');
            if (token && args[1]) {
                args[1].headers = {
                    ...args[1].headers,
                    'Authorization': `Bearer ${token}`
                };
            }
            return originalFetch.apply(this, args);
        };
        
        const user = JSON.parse(sessionStorage.getItem('user') || '{}');
        if (user.username) {
            document.getElementById('username-display').textContent = user.username + ' (' + user.role + ')';
        }
        
        function handleLogout() {
            localStorage.removeItem('auth_token');
            sessionStorage.removeItem('user');
            window.location.href = '/login';
        }
    </script>
    
    @yield('scripts')
</body>
</html>
