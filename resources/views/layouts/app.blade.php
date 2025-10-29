<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Admin Panel') }} - @yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <aside class="w-64 bg-gray-800 text-white min-h-screen p-4">
            <div class="mb-8">
                <h1 class="text-2xl font-bold">Admin Panel</h1>
                <p class="text-sm text-gray-400 mt-2" id="username-display"></p>
            </div>
            <nav>
                <ul class="space-y-2">
                    <li>
                        <a href="/admin/dashboard" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is('admin/dashboard') ? 'bg-gray-700' : '' }}">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="/admin/apps" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is('admin/apps*') ? 'bg-gray-700' : '' }}">
                            Apps
                        </a>
                    </li>
                    <li>
                        <a href="/admin/admob-accounts" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is('admin/admob-accounts*') ? 'bg-gray-700' : '' }}">
                            AdMob Accounts
                        </a>
                    </li>
                    <li>
                        <a href="/admin/notifications" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is('admin/notifications*') ? 'bg-gray-700' : '' }}">
                            Notifications
                        </a>
                    </li>
                    <li>
                        <a href="/admin/analytics" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->is('admin/analytics*') ? 'bg-gray-700' : '' }}">
                            Analytics
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="mt-auto pt-8">
                <button onclick="handleLogout()" class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Logout
                </button>
            </div>
        </aside>
        
        <script>
        const user = JSON.parse(sessionStorage.getItem('user') || '{}');
        if (user.username) {
            document.getElementById('username-display').textContent = user.username + ' (' + user.role + ')';
        }
        
        function handleLogout() {
            fetch('/api/auth/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'include'
            })
            .then(() => {
                sessionStorage.removeItem('user');
                window.location.href = '/login';
            })
            .catch(error => {
                console.error('Logout error:', error);
                sessionStorage.removeItem('user');
                window.location.href = '/login';
            });
        }
        </script>

        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
