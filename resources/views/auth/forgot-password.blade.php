<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ app_abbreviation() }} | Forgot Password</title>

    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/vendor/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Tabler Icons -->
    <link rel="stylesheet" href="{{ asset('css/vendor/tabler-icons.min.css') }}">

    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #FDF8F0;
        }

        .login-split {
            display: flex;
            min-height: 100vh;
        }

        .login-form-panel {
            flex: 0 0 46%;
            max-width: 46%;
            display: flex;
            flex-direction: column;
            padding: 2.5rem 4rem;
            background: #FDF8F0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 700;
            font-size: 1.1rem;
            color: #1E3A5F;
        }

        .brand .brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 0.5rem;
            background: #1E3A5F;
            color: #FDF8F0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .login-form-inner {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 380px;
            width: 100%;
            margin: 0 auto;
        }

        .login-form-inner h3 {
            font-size: 1.65rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
            color: #1E3A5F;
        }

        .login-form-inner > p {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            color: #374151;
        }

        .input-group-text {
            background: #FAF6EE;
            border-right: none;
            border-color: #E8E2D5;
            color: #9ca3af;
        }

        .form-control {
            border-left: none;
            border-color: #E8E2D5;
            background: #FAF6EE;
            padding: 0.625rem 0.75rem;
        }

        .form-control:focus {
            background: #FAF6EE;
            border-color: #3B82F6;
            box-shadow: none;
        }

        .input-group:focus-within {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
            border-radius: 0.5rem;
        }

        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: #1E3A5F;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-login:hover {
            background: #3B82F6;
            transform: translateY(-1px);
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .register-link a {
            color: #1E3A5F;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .alert-danger {
            background: #FEE2E2;
            border: 1px solid #FECACA;
            color: #DC2626;
        }

        .alert-success {
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            color: #059669;
        }

        .login-image-panel {
            flex: 1;
            position: relative;
            background-image: url('{{ asset('images/university-bg.png') }}');
            background-size: cover;
            background-position: center;
        }

        .login-image-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(30, 58, 95, 0.35) 0%, rgba(30, 58, 95, 0) 35%);
        }

        .login-image-panel .caption {
            position: absolute;
            left: 3rem;
            right: 3rem;
            bottom: 3rem;
            color: white;
            z-index: 1;
        }

        .login-image-panel .caption h2 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-image-panel .caption p {
            opacity: 0.9;
            font-size: 0.9rem;
            line-height: 1.5;
            max-width: 32rem;
        }

        @media (max-width: 900px) {
            .login-image-panel {
                display: none;
            }
            .login-form-panel {
                flex: 1;
                max-width: 100%;
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-split">
        <div class="login-image-panel">
            <div class="caption">
                <h2>Staff Management System</h2>
                <p>University Leave Management System - simplifying leave requests, approvals, and tracking for academic and administrative staff.</p>
            </div>
        </div>

        <div class="login-form-panel">
            <div class="brand">
                <span class="brand-icon"><i class="ti ti-building-bank"></i></span>
                Staff Management System
            </div>

            <div class="login-form-inner">
                <h3>Forgot Password</h3>
                <p>Enter your email and we'll send you a link to reset your password.</p>

                <x-alert-success />

                @error('email')
                <x-alert-danger :session="false">
    {{ $message }}
</x-alert-danger>
                @enderror

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-mail"></i></span>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   placeholder="Enter your registered email"
                                   value="{{ old('email') }}"
                                   required autofocus>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="ti ti-send me-1"></i> Send Reset Link
                    </button>

                    <div class="register-link">
                        <a href="{{ route('login') }}"><i class="ti ti-arrow-left me-1"></i> Back to Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/vendor/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
