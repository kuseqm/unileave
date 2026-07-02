<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UniLeave | @yield('title')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    
    <!-- Ember System Standards -->
    <link rel="stylesheet" href="{{ asset('css/ember-standards.css') }}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Cormorant+Garamond:wght@400;600;700&family=Cinzel:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Layout Container */
        .app-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        
        /* Sidebar Area - FLEX COLUMN */
        .sidebar-area {
            width: 260px;
            background: #fdf8f4 !important;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100vh;
            flex-shrink: 0;
        }
        
        .sidebar-area.collapsed {
            width: 70px;
        }
        
        /* Content Area */
        .content-area {
            flex: 1;
            background: #fdf8f4 !important;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow-y: auto;
        }
        
        /* Sidebar Top Bar - FIXED */
        .sidebar-topbar {
            height: 60px;
            background: #fdf8f4 !important;
            border-bottom: 1px solid #E8E2D5;
            display: flex;
            align-items: center;
            padding: 0 1rem;
            flex-shrink: 0;
        }
        
        /* Circular Toggle Button */
        .sidebar-toggle {
            width: 32px;
            height: 32px;
            background: #C4D9EE;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #475569;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.2s;
            margin-right: 12px;
        }
        
        .sidebar-toggle:hover {
            background: #B0C8E0;
            color: #1E293B;
        }
        
        /* Sidebar Logo Area */
        .sidebar-logo {
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-logo h3 {
            color: #1E293B;
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
            line-height: 1.2;
        }
        
        .sidebar-logo .logo-blue {
            color: #3B82F6;
            font-weight: 700;
        }
        
        .sidebar-logo small {
            color: #64748B;
            font-size: 0.65rem;
            letter-spacing: 1px;
        }
        
        /* Main Content */
        .main-content {
            padding: 1rem;
            flex: 1;
            background: #fdf8f4 !important;
        }
        
        /* Card Styles */
        .card {
            border: 1px solid #E8E2D5;
            background: white;
            margin-bottom: 0.75rem;
        }
        
        .card-header {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #E8E2D5;
            background: transparent;
            font-weight: 600;
        }
        
        .card-body {
            padding: 0.5rem 0.75rem;
        }
        
        /* When sidebar collapsed */
        .sidebar-area.collapsed .sidebar-logo h3,
        .sidebar-area.collapsed .sidebar-logo small {
            display: none;
        }
        
        .sidebar-area.collapsed .sidebar-topbar {
            justify-content: center;
        }
        
        .sidebar-area.collapsed .sidebar-toggle {
            margin-right: 0;
        }
        
        /* Sticky Banner Styles */
        .sticky-banner {
            position: sticky;
            top: 60px;
            z-index: 98;
            background: #FEF3C7;
            border-bottom: 1px solid #F59E0B;
            padding: 0.5rem 1rem;
            animation: slideDown 0.3s ease-out;
            flex-shrink: 0;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar-area {
                position: fixed;
                z-index: 1000;
                transform: translateX(-100%);
            }
            .sidebar-area.show {
                transform: translateX(0);
            }
            .content-area {
                width: 100%;
            }
            
            .sticky-banner .d-flex {
                flex-direction: column;
                gap: 0.5rem;
                align-items: flex-start !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="app-container">
        
        <!-- Sidebar Area -->
        <div class="sidebar-area" id="sidebarArea">
            <!-- Sidebar Top Bar - FIXED AT TOP -->
            <div class="sidebar-topbar">
                <div class="sidebar-toggle" id="sidebarToggle">
                    <i class="ti ti-chevron-left" id="toggleIcon"></i>
                </div>
                <div class="sidebar-logo">
                    <h3>Uni<span class="logo-blue">Leave</span></h3>
                    <small>LEAVE MANAGEMENT</small>
                </div>
            </div>
            
            <!-- Sidebar Navigation - SCROLLABLE AREA -->
            <div class="sidebar-nav-container">
                @include('layouts.partials.sidebar')
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
            <!-- Content Top Bar - NOW A COMPONENT -->
            <x-topbar />
            
            <!-- ============================================ -->
            <!-- STICKY BANNER WITH APPROVAL TRACKER (ADDED) -->
            <!-- ============================================ -->
            @auth
            @php
                $pendingRequest = Auth::user()->leaveRequests()
                    ->where('status', 'pending')
                    ->where('acting_status', '!=', 'declined')
                    ->latest()
                    ->first();
            @endphp
            
            @if($pendingRequest)
            <div class="sticky-banner">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <i class="ti ti-alert-triangle" style="color: #D97706; font-size: 1.2rem;"></i>
                        <span class="small fw-medium" style="color: #92400E;">Your leave request is pending approval</span>
                        <x-approval-tracker-mini :leaveRequest="$pendingRequest" />
                    </div>
                    <a href="{{ route('leave-requests.show', $pendingRequest->id) }}" class="small text-decoration-none" style="color: #D97706; font-weight: 500;">
                        View Details <i class="ti ti-arrow-right"></i>
                    </a>
                </div>
            </div>
            @endif
            @endauth
            
            <!-- Main Content -->
            <div class="main-content">
                @yield('content')
            </div>
        </div>
        
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar Toggle Functionality
        const sidebarArea = document.getElementById('sidebarArea');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const toggleIcon = document.getElementById('toggleIcon');
        
        function updateToggleIcon() {
            if (sidebarArea.classList.contains('collapsed')) {
                toggleIcon.classList.remove('ti-chevron-left');
                toggleIcon.classList.add('ti-chevron-right');
            } else {
                toggleIcon.classList.remove('ti-chevron-right');
                toggleIcon.classList.add('ti-chevron-left');
            }
        }
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebarArea.classList.toggle('collapsed');
                updateToggleIcon();
                localStorage.setItem('sidebarCollapsed', sidebarArea.classList.contains('collapsed'));
            });
        }
        
        // Load saved state
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebarArea.classList.add('collapsed');
            updateToggleIcon();
        }
        
        // Mobile sidebar toggle
        if (window.innerWidth <= 768) {
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            overlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999; display: none;';
            document.body.appendChild(overlay);
            
            document.querySelector('.sidebar-toggle')?.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebarArea.classList.toggle('show');
                overlay.style.display = sidebarArea.classList.contains('show') ? 'block' : 'none';
            });
            
            overlay.addEventListener('click', function() {
                sidebarArea.classList.remove('show');
                overlay.style.display = 'none';
            });
        }
        
        // Redirect to dashboard if at root
        if (window.location.pathname === '/' || window.location.pathname === '/admin/dashboard') {
            window.location.href = '/dashboard';
        }
    </script>
    
    <!-- Global Delete Modal Function -->
    <script>
    function showDeleteModal(itemId, itemName, itemType, itemRoute) {
        // For modals that are dynamically added or need to be found
        let modal = document.getElementById('deleteModal' + itemId);
        
        // If modal doesn't exist, create a temporary one
        if (!modal) {
            // Create modal HTML with type-specific warnings
            let typeWarning = '';
            
            if (itemType === 'blackout') {
                typeWarning = `
                    <div class="alert alert-info py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                        <i class="ti ti-calendar me-1"></i> <strong>Impact:</strong><br>
                        - Staff will be able to apply for leave during this period<br>
                        - No leave requests will be affected (only future applications)
                    </div>
                `;
            } else if (itemType === 'university') {
                typeWarning = `
                    <div class="alert alert-danger py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                        <i class="ti ti-building me-1"></i> <strong>This will permanently delete:</strong><br>
                        - All campuses under this university<br>
                        - All departments under those campuses<br>
                        - All staff assignments to those departments
                    </div>
                `;
            } else if (itemType === 'campus') {
                typeWarning = `
                    <div class="alert alert-danger py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                        <i class="ti ti-map-pin me-1"></i> <strong>This will permanently delete:</strong><br>
                        - All departments under this campus<br>
                        - All staff assignments to those departments
                    </div>
                `;
            } else if (itemType === 'department') {
                typeWarning = `
                    <div class="alert alert-danger py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                        <i class="ti ti-building-community me-1"></i> <strong>This will permanently delete:</strong><br>
                        - All staff assignments to this department<br>
                        - Staff will need to be reassigned
                    </div>
                `;
            } else if (itemType === 'leave_type') {
                typeWarning = `
                    <div class="alert alert-warning py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                        <i class="ti ti-calendar-stats me-1"></i> <strong>Note:</strong><br>
                        - Existing leave requests of this type will NOT be deleted<br>
                        - They will show as "Unknown Type"<br>
                        - Future applications cannot use this type
                    </div>
                `;
            } else if (itemType === 'staff') {
                typeWarning = `
                    <div class="alert alert-danger py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                        <i class="ti ti-user-x me-1"></i> <strong>This will:</strong><br>
                        - Remove all access for this staff member<br>
                        - Archive their leave request history<br>
                        - Remove them from acting staff assignments
                    </div>
                `;
            } else {
                typeWarning = `
                    <div class="alert alert-danger py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                        <i class="ti ti-alert-circle me-1"></i> This action cannot be undone.
                    </div>
                `;
            }
            
            const modalHtml = `
                <div class="modal fade" id="deleteModal${itemId}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-sm">
                            <div class="modal-header py-1 px-2 bg-danger-subtle">
                                <h5 class="modal-title" style="font-size: 0.8rem;">
                                    <i class="ti ti-alert-triangle text-danger me-1"></i> Confirm Delete
                                </h5>
                                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body py-2 px-2">
                                <p style="font-size: 0.75rem;">Are you sure you want to delete <strong>${itemName}</strong>?</p>
                                ${typeWarning}
                            </div>
                            <div class="modal-footer py-1 px-2">
                                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">
                                    <i class="ti ti-x me-1"></i> Cancel
                                </button>
                                <form action="${itemRoute}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">
                                        <i class="ti ti-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Append to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            modal = document.getElementById('deleteModal' + itemId);
        }
        
        // Update the form action just in case
        const form = modal.querySelector('form');
        if (form) {
            form.action = itemRoute;
        }
        
        // Update the item name
        const messageSpan = modal.querySelector('.modal-body p');
        if (messageSpan) {
            messageSpan.innerHTML = 'Are you sure you want to delete <strong>' + itemName + '</strong>?';
        }
        
        // Show the modal
        new bootstrap.Modal(modal).show();
    }
    </script>
    
    @stack('scripts')
</body>
</html>