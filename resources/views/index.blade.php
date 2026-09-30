<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ app_abbreviation() }} | Dashboard</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Master CSS -->
    <link rel="stylesheet" href="{{ asset('css/unileave.css') }}">
    
    <style>
        body {
            background: var(--bg-light);
        }
        .welcome-card {
            max-width: 600px;
            margin: 100px auto;
            text-align: center;
            padding: var(--spacing-8);
        }
        .user-name {
            color: var(--primary-blue);
            margin-bottom: var(--spacing-4);
        }
        .logout-btn {
            margin-top: var(--spacing-6);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card welcome-card">
            <div class="card-body">
                <div class="stat-card-icon" style="margin: 0 auto 20px; width: 80px; height: 80px;">
                    <i class="fas fa-check-circle" style="font-size: 48px; color: var(--success-green);"></i>
                </div>
                
                <h1 class="user-name">Welcome, {{ Auth::user()->first_name }}! 👋</h1>
                <p class="text-medium">You have successfully logged into Staff Management System.</p>
                
                <div class="logout-btn">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>