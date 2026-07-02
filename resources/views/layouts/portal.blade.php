<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        
        .portal-container {
            width: 100%;
            max-width: 900px;
        }
        
        /* Header */
        .portal-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .portal-header .logo-icon {
            display: inline-block;
            width: 64px;
            height: 64px;
            background: #3B82F6;
            border-radius: 16px;
            color: white;
            font-size: 2rem;
            line-height: 64px;
            text-align: center;
            margin-bottom: 0.75rem;
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.25);
        }
        
        .portal-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 0.25rem;
        }
        
        .portal-header h1 span {
            color: #3B82F6;
        }
        
        .portal-header .welcome-text {
            color: #64748B;
            font-size: 0.95rem;
        }
        
        .portal-header .welcome-text strong {
            color: #1E293B;
        }
        
        /* Systems Grid */
        .portal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .system-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.75rem 1.25rem;
            text-align: center;
            border: 1px solid #E8E2D5;
            transition: all 0.25s ease;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            cursor: pointer;
        }
        
        .system-card:hover:not(.coming-soon) {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.08);
            border-color: #3B82F6;
        }
        
        .system-card .icon-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 0.75rem;
            flex-shrink: 0;
        }
        
        .bg-primary { background: #3B82F6; }
        .bg-info { background: #06B6D4; }
        .bg-warning { background: #F59E0B; }
        .bg-success { background: #10B981; }
        .bg-danger { background: #EF4444; }
        .bg-secondary { background: #94A3B8; }
        
        .system-card .system-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1E293B;
            margin-bottom: 0.2rem;
        }
        
        .system-card .system-desc {
            font-size: 0.7rem;
            color: #94A3B8;
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }
        
        .system-card .btn-access {
            font-size: 0.65rem;
            font-weight: 500;
            padding: 0.35rem 1.25rem;
            border-radius: 0.5rem;
            border: none;
            color: white;
            transition: all 0.2s ease;
            cursor: pointer;
            display: inline-block;
            background: #94A3B8;
        }
        
        .system-card .btn-access.btn-primary { background: #3B82F6; }
        .system-card .btn-access.btn-info { background: #06B6D4; }
        .system-card .btn-access.btn-warning { background: #F59E0B; }
        .system-card .btn-access.btn-success { background: #10B981; }
        .system-card .btn-access.btn-danger { background: #EF4444; }
        .system-card .btn-access.btn-secondary { background: #94A3B8; }
        
        .system-card .btn-access:hover {
            transform: scale(1.04);
        }
        
        /* Coming Soon */
        .system-card.coming-soon {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .system-card.coming-soon:hover {
            transform: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        
        .system-card .coming-badge {
            font-size: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.2rem 0.75rem;
            border-radius: 1rem;
            background: #E8E2D5;
            color: #94A3B8;
        }
        
        /* Footer */
        .portal-footer {
            text-align: center;
            padding-top: 1rem;
        }
        
        .portal-footer .logout-btn {
            font-size: 0.65rem;
            color: #94A3B8;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: color 0.2s ease;
            text-decoration: underline;
            text-underline-offset: 2px;
        }
        
        .portal-footer .logout-btn:hover {
            color: #EF4444;
        }
        
        .portal-footer .divider {
            color: #E8E2D5;
            margin: 0 0.75rem;
        }
        
        .portal-footer .footer-text {
            font-size: 0.6rem;
            color: #CBD5E1;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            body { padding: 1rem; }
            .portal-grid { grid-template-columns: repeat(2, 1fr); }
            .portal-header h1 { font-size: 1.5rem; }
            .portal-header .logo-icon { width: 48px; height: 48px; font-size: 1.5rem; line-height: 48px; }
            .system-card { padding: 1.25rem 0.75rem; }
        }
        
        @media (max-width: 480px) {
            .portal-grid { grid-template-columns: 1fr 1fr; }
            .system-card { padding: 1rem 0.5rem; }
            .system-card .icon-circle { width: 44px; height: 44px; font-size: 1.2rem; }
            .system-card .system-name { font-size: 0.75rem; }
            .system-card .system-desc { font-size: 0.55rem; }
            .portal-header h1 { font-size: 1.25rem; }
        }
    </style>
</head>
<body>
    <div class="portal-container">
        <!-- Header -->
        <div class="portal-header">
            <div class="logo-icon">
                <i class="ti ti-school"></i>
            </div>
            <h1>Uni<span>Portal</span></h1>
            <p class="welcome-text">Welcome, <strong>{{ auth()->user()->first_name ?? auth()->user()->name ?? 'User' }}</strong></p>
        </div>
        
        <!-- Systems Grid -->
        <div class="portal-grid">
            @yield('systems')
        </div>
        
        <!-- Footer -->
        <div class="portal-footer">
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="ti ti-logout"></i> Logout
                </button>
            </form>
            <span class="divider">|</span>
            <span class="footer-text">{{ date('Y') }} UniLeave</span>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>