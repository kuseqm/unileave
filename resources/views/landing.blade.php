<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniLeave - University Leave Management System</title>
    
    <!-- Tabler CSS (Local - Already installed) -->
    <link rel="stylesheet" href="{{ asset('tabler/tabler.min.css') }}">
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #1E3A5F 0%, #2E5A8A 100%);
        }
        .feature-icon {
            width: 64px;
            height: 64px;
            background: #EFF6FF;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bg-primary-light {
            background-color: #EFF6FF;
        }
        .py-6 {
            padding-top: 4rem;
            padding-bottom: 4rem;
        }
        .btn-verify-nav {
            border-radius: 50px;
            padding: 0.4rem 1rem;
            margin-left: 0.5rem;
            transition: all 0.3s;
        }
        .btn-verify-nav:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-2" href="/">
                Uni<span class="text-warning">Leave</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#leave-types">Leave Types</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <!-- Verify Document - Blue button style -->
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-verify-nav ms-2" href="{{ route('verify.index') }}">
                            <i class="ti ti-qrcode me-1"></i> Verify Document
                        </a>
                    </li>
                </ul>
                <div class="ms-3">
                    <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Sign In</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-6">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold">Manage University Leave<br><span class="text-warning">Effortlessly</span></h1>
                    <p class="lead mt-3 opacity-75">UniLeave streamlines leave requests, approvals, and tracking for thousands of staff members across multiple campuses.</p>
                    <div class="mt-4">
                        <a href="{{ route('register') }}" class="btn btn-warning btn-lg me-2">Get Started →</a>
                        <a href="#features" class="btn btn-outline-light btn-lg">Learn More</a>
                    </div>
                    <div class="mt-5 d-flex gap-4">
                        <div><span class="fw-bold fs-3">5000+</span><br>Staff Members</div>
                        <div><span class="fw-bold fs-3">11</span><br>Leave Types</div>
                        <div><span class="fw-bold fs-3">24/7</span><br>Access</div>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0 text-center">
                    <div class="bg-white rounded-4 p-4 d-inline-block shadow-lg">
                        <i class="ti ti-calendar-stats text-primary" style="font-size: 120px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Stats Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <i class="ti ti-users fs-1 text-primary"></i>
                            <h2 class="mt-2 fw-bold">5000+</h2>
                            <p class="text-muted">Staff Members</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <i class="ti ti-calendar fs-1 text-primary"></i>
                            <h2 class="mt-2 fw-bold">11</h2>
                            <p class="text-muted">Leave Types</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <i class="ti ti-clock fs-1 text-primary"></i>
                            <h2 class="mt-2 fw-bold">24/7</h2>
                            <p class="text-muted">Online Access</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <i class="ti ti-chart-bar fs-1 text-primary"></i>
                            <h2 class="mt-2 fw-bold">100%</h2>
                            <p class="text-muted">Paperless</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section id="features" class="py-6 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Why Choose UniLeave?</h2>
                <p class="lead text-muted">A comprehensive solution designed specifically for university administration</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="feature-icon mb-3">
                                <i class="ti ti-cloud-upload fs-1 text-primary"></i>
                            </div>
                            <h4>Online Applications</h4>
                            <p class="text-muted">Apply for leave anytime, anywhere. Upload required documents securely.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="feature-icon mb-3">
                                <i class="ti ti-chart-line fs-1 text-primary"></i>
                            </div>
                            <h4>Real-time Tracking</h4>
                            <p class="text-muted">See exactly where your request is in the approval workflow.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="feature-icon mb-3">
                                <i class="ti ti-bell fs-1 text-primary"></i>
                            </div>
                            <h4>Instant Notifications</h4>
                            <p class="text-muted">Get email and in-app notifications for every status change.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="feature-icon mb-3">
                                <i class="ti ti-file-description fs-1 text-primary"></i>
                            </div>
                            <h4>Comprehensive Reports</h4>
                            <p class="text-muted">Generate Excel, CSV, and PDF reports.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="feature-icon mb-3">
                                <i class="ti ti-device-mobile fs-1 text-primary"></i>
                            </div>
                            <h4>Mobile Ready</h4>
                            <p class="text-muted">Install as an app on your Android phone.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="feature-icon mb-3">
                                <i class="ti ti-shield fs-1 text-primary"></i>
                            </div>
                            <h4>Secure & Compliant</h4>
                            <p class="text-muted">Compliant with Tanzanian labour laws.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Leave Types Section -->
    <section id="leave-types" class="py-6 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Leave Types Supported</h2>
                <p class="lead text-muted">11 different leave types with configurable document requirements</p>
            </div>
            <div class="text-center">
                <span class="badge bg-primary-light text-primary m-1 p-2 px-3">LC1 - Annual Leave</span>
                <span class="badge bg-primary-light text-primary m-1 p-2 px-3">LC2 - Sick Leave</span>
                <span class="badge bg-primary-light text-primary m-1 p-2 px-3">LC3 - Compassionate</span>
                <span class="badge bg-primary-light text-primary m-1 p-2 px-3">LC4 - Maternity/Paternity</span>
                <span class="badge bg-primary-light text-primary m-1 p-2 px-3">LC5 - Special Leave</span>
                <span class="badge bg-primary-light text-primary m-1 p-2 px-3">LC6 - Study Leave</span>
                <span class="badge bg-primary-light text-primary m-1 p-2 px-3">LC7 - Sabbatical</span>
                <span class="badge bg-primary-light text-primary m-1 p-2 px-3">LC8 - Unpaid Leave</span>
                <span class="badge bg-primary-light text-primary m-1 p-2 px-3">LC9 - Care's Leave</span>
                <span class="badge bg-primary-light text-primary m-1 p-2 px-3">LC10 - University Business</span>
                <span class="badge bg-primary-light text-primary m-1 p-2 px-3">LC11 - Custom Types</span>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-6 bg-primary text-white">
        <div class="container text-center">
            <h2 class="fw-bold fs-1">Ready to Simplify Leave Management?</h2>
            <p class="lead mt-2 opacity-75">Join thousands of staff members already using UniLeave</p>
            <a href="{{ route('login') }}" class="btn btn-light btn-lg px-5 mt-3">Get Started Now →</a>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="py-4 bg-dark text-white-50">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} UniLeave. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Tabler JS (Local) -->
    <script src="{{ asset('tabler/tabler.min.js') }}"></script>
</body>
</html>