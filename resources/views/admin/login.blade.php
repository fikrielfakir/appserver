<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - {{ config('app.name', 'Admin Panel') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h1 class="h3 mb-4 text-center fw-bold">Admin Login</h1>
                        
                        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
                        
                        <form id="login-form" onsubmit="handleLogin(event)">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-bold">Username</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="username" 
                                    name="username"
                                    required
                                >
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    name="password"
                                    autocomplete="current-password"
                                    required
                                >
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Sign In
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function handleLogin(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const errorDiv = document.getElementById('error-message');
        
        fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                username: formData.get('username'),
                password: formData.get('password')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.token) {
                // Store token and user info
                localStorage.setItem('auth_token', data.token);
                sessionStorage.setItem('user', JSON.stringify(data.user));
                window.location.href = '/admin/dashboard';
            } else {
                errorDiv.textContent = data.message || 'Login failed';
                errorDiv.classList.remove('d-none');
            }
        })
        .catch(error => {
            errorDiv.textContent = 'An error occurred. Please try again.';
            errorDiv.classList.remove('d-none');
            console.error('Login error:', error);
        });
    }
    </script>
</body>
</html>
