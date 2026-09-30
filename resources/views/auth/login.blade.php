<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ app_abbreviation() }} | Login</title>

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

        /* Left Panel - Form Side */
        .login-form-panel {
            flex: 0 0 46%;
            max-width: 46%;
            display: flex;
            flex-direction: column;
            padding: 2.5rem 4rem;
            background: #FDF8F0;
        }

        .brand {
            font-weight: 900;
            font-size: 1.3rem;
            color: #1B1926;
        }

        .brand .accent { color: #96771A; }

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

        .form-check {
            margin: 1rem 0;
        }

        .form-check-label {
            font-size: 0.875rem;
            color: #6c757d;
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

        /* Right Panel - Full-bleed, slowly rotating photo (same slides as
           the landing page hero and the verify pages, for one consistent
           look instead of a generic stock illustration). */
        .login-image-panel {
            flex: 1;
            position: relative;
            overflow: hidden;
        }

        .login-image-slides { position: absolute; inset: 0; z-index: 0; }
        .login-image-slide {
            position: absolute; inset: 0;
            background-size: cover; background-position: center;
            opacity: 0; transition: opacity 1.8s ease-in-out;
        }
        .login-image-slide.is-active { opacity: 1; }

        .login-image-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 1;
            background: linear-gradient(to top, rgba(15, 28, 69, 0.88) 0%, rgba(15, 28, 69, 0.25) 55%, rgba(15, 28, 69, 0.05) 100%);
        }

        .login-image-panel .caption {
            position: absolute;
            left: 3rem;
            right: 3rem;
            bottom: 3rem;
            color: white;
            z-index: 2;
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

        .login-image-panel .stats {
            display: flex;
            gap: 2rem;
            margin-top: 1.5rem;
        }

        .login-image-panel .stat-number {
            font-size: 1.4rem;
            font-weight: 700;
        }

        .login-image-panel .stat-label {
            font-size: 0.75rem;
            opacity: 0.85;
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
        <!-- Left Panel - Full-bleed Image Side -->
        <div class="login-image-panel">
            <div class="login-image-slides">
                @foreach(['campus.jpg', 'campus-2.jpg', 'campus-3.jpg', 'campus-4.jpg'] as $index => $slide)
                    @if(file_exists(public_path('images/landing/hero/'.$slide)))
                        <div class="login-image-slide {{ $index === 0 ? 'is-active' : '' }}" style="background-image: url('{{ asset('images/landing/hero/'.$slide) }}');"></div>
                    @endif
                @endforeach
            </div>
            <div class="caption">
                <div class="mb-2"><x-academic-year-badge dark /></div>
                <h2>Staff Management System</h2>
                <p>University Leave Management System - simplifying leave requests, approvals, and tracking for academic and administrative staff.</p>
                <div class="stats">
                    <div>
                        <div class="stat-number">{{ number_format($staffCount) }}</div>
                        <div class="stat-label">Staff Members</div>
                    </div>
                    <div>
                        <div class="stat-number">{{ $leaveTypeCount }}</div>
                        <div class="stat-label">Leave Types</div>
                    </div>
                    <div>
                        <div class="stat-number">{{ $departmentCount }}</div>
                        <div class="stat-label">Departments</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Form Side -->
        <div class="login-form-panel">
            <div class="brand">Staff<span class="accent">MS</span></div>

            <div class="login-form-inner">
                <h3>Welcome Back</h3>
                <p>Please login to your account</p>

                <x-alert-danger />

                @if(session('success'))
                <div class="alert" style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #059669;">
                    <i class="ti ti-circle-check me-1"></i> {{ session('success') }}
                </div>
                @endif

                @if(session('info'))
                <div class="alert" style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #1D4ED8;">
                    <i class="ti ti-info-circle me-1"></i> {{ session('info') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Email or Staff ID</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-user"></i></span>
                            <input type="text"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="Enter your Email or Staff ID"
                                   value="{{ old('email') }}"
                                   required>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0">Password</label>
                            <a href="{{ route('password.request') }}" style="font-size: 0.8rem; color: #3B82F6; text-decoration: none;">Forgot password?</a>
                        </div>
                        <div class="input-group mt-2">
                            <span class="input-group-text"><i class="ti ti-lock"></i></span>
                            <input type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Enter your password"
                                   required>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="terms" value="1" class="form-check-input @error('terms') is-invalid @enderror" id="terms">
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="{{ route('legal.terms') }}" target="_blank">Terms of Service</a> and <a href="{{ route('legal.privacy') }}" target="_blank">Privacy Policy</a>
                        </label>
                        @error('terms')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="ti ti-login me-1"></i> Sign In
                    </button>

                    <div class="register-link">
                        Don't have an account? <a href="{{ route('register') }}">Create an account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/vendor/bootstrap.bundle.min.js') }}"></script>
    <script>
        (function () {
            var slides = document.querySelectorAll('.login-image-slide');
            if (slides.length < 2) return;
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

            var current = 0;
            setInterval(function () {
                slides[current].classList.remove('is-active');
                current = (current + 1) % slides.length;
                slides[current].classList.add('is-active');
            }, 5000);
        })();
    </script>
</body>
</html>
