<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'UniLeave') }} - Document Verification</title>
    <!-- Tabler CSS -->
    <link rel="stylesheet" href="{{ asset('tabler/tabler.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <style>
        body {
            background: #f2f4f8;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .verification-container {
            max-width: 800px;
            margin: 60px auto;
            padding: 0 20px;
        }
        .system-name {
            text-align: center;
            margin-bottom: 30px;
        }
        .system-name h2 {
            font-weight: 700;
            color: #1E3A5F;
            letter-spacing: 1px;
        }
        .system-name p {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .card-verify {
            border: 1px solid #E8E2D5;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            overflow: hidden;
            background: white;
        }
        .card-verify .card-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #E8E2D5;
            padding: 0.75rem 1rem;
            font-weight: 600;
            font-size: 0.9rem;
            color: #1E3A5F;
        }
        .card-verify .card-header i {
            margin-right: 0.5rem;
        }
        .btn-primary {
            background-color: #1E3A5F;
            border-color: #1E3A5F;
        }
        .btn-primary:hover {
            background-color: #2E5A8A;
            border-color: #2E5A8A;
        }
        .btn-outline-primary {
            border-color: #1E3A5F;
            color: #1E3A5F;
        }
        .btn-outline-primary:hover {
            background-color: #1E3A5F;
            color: white;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="verification-container">
        <div class="system-name">
            <h2>{{ config('app.name', 'UniLeave') }}</h2>
            <p>Official Document Verification Portal</p>
        </div>
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>