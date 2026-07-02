<!-- Sidebar Navigation - Scrollable Menu -->
<div class="sidebar-nav-scroll">
    <nav class="sidebar-nav">
        <div class="nav flex-column">
            
            <!-- 1. Overview Section (with Portal Home as submenu) -->
            <div class="nav-section">
                <div class="nav-section-header" data-section="overview">
                    <div class="nav-section-title">
                        <i class="ti ti-home"></i>
                        <span>Overview</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="overview">
                    <!-- Portal Home as submenu item -->
                    <a class="nav-link nav-sub-link {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}" href="{{ route('portal.dashboard') }}">
                        <i class="ti ti-home"></i>
                        <span>Portal Home</span>
                    </a>
                    <a class="nav-link nav-sub-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="ti ti-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>
            
            <!-- 2. Institute Section (Admin only) -->
            @role('admin')
            <div class="nav-section">
                <div class="nav-section-header" data-section="institute">
                    <div class="nav-section-title">
                        <i class="ti ti-building"></i>
                        <span>Institute</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="institute">
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.universities*') ? 'active' : '' }}" href="{{ route('admin.universities.index') }}">
                        <i class="ti ti-building-community"></i>
                        <span>Universities</span>
                    </a>
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.campuses*') ? 'active' : '' }}" href="{{ route('admin.campuses.index') }}">
                        <i class="ti ti-map-pin"></i>
                        <span>Campuses</span>
                    </a>
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.schools*') ? 'active' : '' }}" href="{{ route('admin.schools.index') }}">
                        <i class="ti ti-school"></i>
                        <span>Schools/Faculties</span>
                    </a>
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.departments*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">
                        <i class="ti ti-folder"></i>
                        <span>Departments</span>
                    </a>
                </div>
            </div>
            @endrole
            
            <!-- 3. Human Resources Section -->
            @hasanyrole('admin|hr-officer|hod|dean|dvca|dvc-pfa|unit_head|vice-chancellor')
            <div class="nav-section">
                <div class="nav-section-header" data-section="hr">
                    <div class="nav-section-title">
                        <i class="ti ti-users"></i>
                        <span>Human Resources</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="hr">
                    <a class="nav-link nav-sub-link {{ request()->routeIs('staff*') ? 'active' : '' }}" href="{{ route('staff.index') }}">
                        <i class="ti ti-user"></i>
                        <span>All Staff</span>
                    </a>
                    
                    @hasanyrole('admin|hr-officer')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.import.staff') ? 'active' : '' }}" href="{{ route('admin.import.staff') }}">
                        <i class="ti ti-file-import"></i>
                        <span>Import Staff</span>
                    </a>
                    @endhasanyrole
                </div>
            </div>
            @endhasanyrole
            
            <!-- 4. Leave Management Section -->
            <div class="nav-section">
                <div class="nav-section-header" data-section="leave">
                    <div class="nav-section-title">
                        <i class="ti ti-calendar"></i>
                        <span>Leave Management</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="leave">
                    @auth
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.create') ? 'active' : '' }}" href="{{ route('leave-requests.create') }}">
                        <i class="ti ti-plus"></i>
                        <span>Apply for Leave</span>
                    </a>
                    @endauth
                    
                    @auth
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.my-requests') ? 'active' : '' }}" href="{{ route('leave-requests.my-requests') }}">
                        <i class="ti ti-list"></i>
                        <span>My Requests</span>
                    </a>
                    @endauth
                    
                    @auth
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.status') ? 'active' : '' }}" href="{{ route('leave-requests.status') }}">
                        <i class="ti ti-list-details"></i>
                        <span>Request Status</span>
                    </a>
                    @endauth
                    
                    <!-- WAITING APPROVALS - Role-based counter -->
                    @hasanyrole('hod|dean|dvca|dvc-pfa|admin|hr-officer|vice-chancellor')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.pending') ? 'active' : '' }}" href="{{ route('leave-requests.pending') }}">
                        <i class="ti ti-clock"></i>
                        <span>Waiting Approvals</span>
                        @php
                            $user = auth()->user();
                            $role = $user->getRoleNames()->first();
                            $pendingCount = 0;
                            
                            switch ($role) {
                                case 'hod':
                                    $pendingCount = \App\Models\LeaveRequest::where('status', 'pending')
                                        ->where('acting_status', 'accepted')
                                        ->where('current_approval_level', 1)
                                        ->whereHas('staff.department', function($q) use ($user) {
                                            $q->where('hod_staff_id', $user->id);
                                        })
                                        ->count();
                                    break;
                                case 'dean':
                                    $pendingCount = \App\Models\LeaveRequest::where('status', 'pending')
                                        ->where('acting_status', 'accepted')
                                        ->where('current_approval_level', 2)
                                        ->whereHas('staff.department.school', function($q) use ($user) {
                                            $q->where('dean_staff_id', $user->id);
                                        })
                                        ->count();
                                    break;
                                case 'dvca':
                                    $pendingCount = \App\Models\LeaveRequest::where('status', 'pending')
                                        ->where('acting_status', 'accepted')
                                        ->where('current_approval_level', 4)
                                        ->whereHas('staff', function($q) {
                                            $q->where('staff_category', 'academic');
                                        })
                                        ->count();
                                    break;
                                case 'dvc-pfa':
                                    $pendingCount = \App\Models\LeaveRequest::where('status', 'pending')
                                        ->where('acting_status', 'accepted')
                                        ->where('current_approval_level', 4)
                                        ->whereHas('staff', function($q) {
                                            $q->where('staff_category', 'administrative');
                                        })
                                        ->count();
                                    break;
                                case 'hr-officer':
                                case 'hr-manager':
                                    $pendingCount = \App\Models\LeaveRequest::where('status', 'pending')
                                        ->where('acting_status', 'accepted')
                                        ->whereIn('current_approval_level', [3, 5])
                                        ->count();
                                    break;
                                case 'admin':
                                    $pendingCount = \App\Models\LeaveRequest::where('status', 'pending')
                                        ->where('acting_status', 'accepted')
                                        ->count();
                                    break;
                                default:
                                    $pendingCount = 0;
                                    break;
                            }
                        @endphp
                        @if($pendingCount > 0)
                        <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.55rem; padding: 0.2rem 0.5rem;">
                            {{ $pendingCount > 99 ? '99+' : $pendingCount }}
                        </span>
                        @endif
                    </a>
                    @endhasanyrole
                    
                    @role('admin')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.leave-types*') ? 'active' : '' }}" href="{{ route('admin.leave-types.index') }}">
                        <i class="ti ti-tags"></i>
                        <span>Leave Types</span>
                    </a>
                    @endrole
                    
                    <a class="nav-link nav-sub-link {{ request()->routeIs('blackout.*') ? 'active' : '' }}" href="{{ route('blackout.index') }}">
                        <i class="ti ti-ban"></i>
                        <span>Blackout Periods</span>
                    </a>

                    @auth
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.hold-position-for-me') ? 'active' : '' }}" href="{{ route('leave-requests.hold-position-for-me') }}">
                        <i class="ti ti-clock-cog"></i>
                        <span>Acting Requests</span>
                    </a>
                    @endauth
                    
                    @auth
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.i-held-position-for') ? 'active' : '' }}" href="{{ route('leave-requests.i-held-position-for') }}">
                        <i class="ti ti-user-check"></i>
                        <span>My Assignments</span>
                    </a>
                    @endauth

                    @auth
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.approvals-history') ? 'active' : '' }}" href="{{ route('leave-requests.approvals-history') }}">
                        <i class="ti ti-history"></i>
                        <span>My Approvals History</span>
                    </a>
                    @endauth
                </div>
            </div>
            
            <!-- 5. Messaging Section (All authenticated users) -->
            @auth
            <div class="nav-section">
                <div class="nav-section-header" data-section="messages">
                    <div class="nav-section-title">
                        <i class="ti ti-mail"></i>
                        <span>Messages</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="messages">
                    <!-- Basic Messaging -->
                    <a class="nav-link nav-sub-link {{ request()->routeIs('messages.inbox') ? 'active' : '' }}" href="{{ route('messages.inbox') }}">
                        <i class="ti ti-inbox"></i>
                        <span>Inbox</span>
                        <span class="badge bg-danger rounded-pill ms-1" id="unreadMessagesBadge" style="font-size: 0.55rem; padding: 0.1rem 0.4rem; display: none;">0</span>
                    </a>
                    
                    <a class="nav-link nav-sub-link {{ request()->routeIs('messages.create') ? 'active' : '' }}" href="{{ route('messages.create') }}">
                        <i class="ti ti-pencil"></i>
                        <span>Compose</span>
                    </a>
                    
                    <a class="nav-link nav-sub-link {{ request()->routeIs('messages.sent') ? 'active' : '' }}" href="{{ route('messages.sent') }}">
                        <i class="ti ti-send"></i>
                        <span>Sent</span>
                    </a>
                    
                    <a class="nav-link nav-sub-link {{ request()->routeIs('messages.drafts') ? 'active' : '' }}" href="{{ route('messages.drafts') }}">
                        <i class="ti ti-file"></i>
                        <span>Drafts</span>
                    </a>
                    
                    <!-- Divider -->
                    <div class="dropdown-divider my-1"></div>
                    
                    <!-- Message Management Section (Admin/HR/HOD/School Admin/VC) -->
                    @php
                        $user = auth()->user();
                        $role = $user->getRoleNames()->first();
                    @endphp
                    
                    @if(in_array($role, ['admin', 'hr_manager', 'vice-chancellor', 'school_admin', 'hod']))
                        <div class="fw-semibold small text-muted mt-2 mb-1 px-2">
                            <i class="ti ti-database me-1" style="font-size: 0.7rem;"></i> Management
                        </div>
                        
                        <!-- Message Directory -->
                        <a class="nav-link nav-sub-link {{ request()->routeIs('messages.admin-list*') ? 'active' : '' }}" href="{{ route('messages.admin-list') }}">
                            <i class="ti ti-database"></i>
                            <span>Message Directory</span>
                        </a>
                    @endif
                    
                    <!-- VC: Access Requests -->
                    @if(in_array($role, ['vice-chancellor', 'admin']))
                        <a class="nav-link nav-sub-link {{ request()->routeIs('messages.access-requests*') ? 'active' : '' }}" href="{{ route('messages.access-requests') }}">
                            <i class="ti ti-key"></i>
                            <span>Access Requests</span>
                            <span class="badge bg-warning rounded-pill ms-1" id="pendingRequestsBadge" style="font-size: 0.55rem; padding: 0.1rem 0.4rem; display: none;">0</span>
                        </a>
                    @endif
                    
                    <!-- Audit Trail (Admin/HR/VC only) -->
                    <!-- Audit Trail Dashboard -->
                    @if(in_array($role, ['admin', 'hr_manager', 'vice-chancellor', 'internal-auditor']))
                        <a class="nav-link nav-sub-link {{ request()->routeIs('audit-trail*') ? 'active' : '' }}" href="{{ route('audit-trail.index') }}">
                            <i class="ti ti-history"></i>
                            <span>Audit Trail Dashboard</span>
                            @php
                                $recentAudits = \App\Models\UnifiedAuditTrail::whereDate('created_at', today())->count();
                            @endphp
                            @if($recentAudits > 0)
                                <span class="badge bg-danger rounded-pill ms-1">{{ $recentAudits > 99 ? '99+' : $recentAudits }}</span>
                            @endif
                        </a>
                    @endif
                </div>
            </div>
            @endauth
            
            <!-- 6. Permission Management Section -->
            <div class="nav-section">
                <div class="nav-section-header" data-section="permission">
                    <div class="nav-section-title">
                        <i class="ti ti-file-text"></i>
                        <span>Permission Management</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="permission">
                    @auth
                    <a class="nav-link nav-sub-link {{ request()->routeIs('permissions.create') ? 'active' : '' }}" href="{{ route('permissions.create') }}">
                        <i class="ti ti-clock"></i>
                        <span>Apply for Permission</span>
                    </a>
                    @endauth
                    
                    @auth
                    <a class="nav-link nav-sub-link {{ request()->routeIs('travel-permissions.create') ? 'active' : '' }}" href="{{ route('travel-permissions.create') }}">
                        <i class="ti ti-plane"></i>
                        <span>Apply for Travel</span>
                    </a>
                    @endauth
                    
                    @auth
                    <a class="nav-link nav-sub-link {{ request()->routeIs('permissions.my-requests') ? 'active' : '' }}" href="{{ route('permissions.my-requests') }}">
                        <i class="ti ti-list"></i>
                        <span>My Permission Requests</span>
                        @php
                            $permissionPendingCount = \App\Models\PermissionRequest::where('staff_id', auth()->id())
                                ->where('status', 'pending')
                                ->count() ?? 0;
                        @endphp
                        @if($permissionPendingCount > 0)
                        <span class="badge bg-info rounded-pill ms-1" style="font-size: 0.55rem; padding: 0.1rem 0.4rem;">{{ $permissionPendingCount > 99 ? '99+' : $permissionPendingCount }}</span>
                        @endif
                    </a>
                    @endauth
                    
                    @hasanyrole('hod|dean|dvca|dvc-pfa|admin|hr-officer|vice-chancellor')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('permissions.pending') ? 'active' : '' }}" href="{{ route('permissions.pending') }}">
                        <i class="ti ti-clock"></i>
                        <span>Unattended Approvals</span>
                        @php
                            $shortPendingCount = \App\Models\PermissionRequest::where('status', 'pending')->count() ?? 0;
                            $travelPendingCount = \App\Models\TravelPermission::where('status', 'pending')
                                ->where('acting_status', 'accepted')
                                ->count() ?? 0;
                            $totalPending = $shortPendingCount + $travelPendingCount;
                        @endphp
                        @if($totalPending > 0)
                        <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.55rem; padding: 0.1rem 0.4rem;">{{ $totalPending > 99 ? '99+' : $totalPending }}</span>
                        @endif
                    </a>
                    @endhasanyrole
                </div>
            </div>
            
            <!-- 7. Central Registry Section (Admin & Registry Officers & Clerk) -->
            @hasanyrole('admin|registry-officer|registrar|clerk')
            <div class="nav-section">
                <div class="nav-section-header" data-section="central-registry">
                    <div class="nav-section-title">
                        <i class="ti ti-mail"></i>
                        <span>Central Registry</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="central-registry">
                    
                    <!-- Incoming Letters -->
                    <a class="nav-link nav-sub-link {{ request()->routeIs('registry.incoming*') ? 'active' : '' }}" href="{{ route('registry.incoming.index') }}">
                        <i class="ti ti-inbox"></i>
                        <span>Incoming Letters</span>
                        @php
                            $pendingIncomingCount = \App\Models\IncomingLetter::where('status', 'received')->count() ?? 0;
                        @endphp
                        @if($pendingIncomingCount > 0)
                        <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.55rem; padding: 0.1rem 0.4rem;">{{ $pendingIncomingCount > 99 ? '99+' : $pendingIncomingCount }}</span>
                        @endif
                    </a>
                    
                    <!-- New Incoming Letter -->
                    <a class="nav-link nav-sub-link {{ request()->routeIs('registry.incoming.create') ? 'active' : '' }}" href="{{ route('registry.incoming.create') }}">
                        <i class="ti ti-plus"></i>
                        <span>New Incoming Letter</span>
                    </a>
                    
                    <!-- Outgoing Letters -->
                    <a class="nav-link nav-sub-link {{ request()->routeIs('registry.outgoing*') ? 'active' : '' }}" href="{{ route('registry.outgoing.index') }}">
                        <i class="ti ti-send"></i>
                        <span>Outgoing Letters</span>
                        @php
                            $pendingOutgoingCount = \App\Models\OutgoingLetter::where('status', 'pending_approval')->count() ?? 0;
                        @endphp
                        @if($pendingOutgoingCount > 0)
                        <span class="badge bg-warning rounded-pill ms-1" style="font-size: 0.55rem; padding: 0.1rem 0.4rem;">{{ $pendingOutgoingCount > 99 ? '99+' : $pendingOutgoingCount }}</span>
                        @endif
                    </a>
                    
                    <!-- Print Queue -->
                    <a class="nav-link nav-sub-link {{ request()->routeIs('registry.print-queue*') ? 'active' : '' }}" href="{{ route('registry.print-queue.index') }}">
                        <i class="ti ti-printer"></i>
                        <span>Print Queue</span>
                        @php
                            $printQueueCount = \App\Models\PrintQueue::where('status', 'pending')->count() ?? 0;
                        @endphp
                        @if($printQueueCount > 0)
                        <span class="badge bg-info rounded-pill ms-1" style="font-size: 0.55rem; padding: 0.1rem 0.4rem;">{{ $printQueueCount > 99 ? '99+' : $printQueueCount }}</span>
                        @endif
                    </a>
                    
                    <!-- Divider -->
                    <div class="dropdown-divider my-1"></div>
                    
                    <!-- Registry Settings (Admin only) -->
                    @role('admin')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('registry.offices*') ? 'active' : '' }}" href="{{ route('registry.offices.index') }}">
                        <i class="ti ti-building"></i>
                        <span>Manage Offices</span>
                    </a>
                    
                    <a class="nav-link nav-sub-link {{ request()->routeIs('registry.settings') ? 'active' : '' }}" href="{{ route('registry.settings') }}">
                        <i class="ti ti-settings"></i>
                        <span>Registry Settings</span>
                    </a>
                    @endrole
                    
                </div>
            </div>
            @endhasanyrole

            <!-- 8. VC Dashboard (Vice Chancellor only) -->
            @role('vice-chancellor')
            <div class="nav-section">
                <div class="nav-section-header" data-section="vc">
                    <div class="nav-section-title">
                        <i class="ti ti-crown"></i>
                        <span>VC Office</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="vc">
                    <a class="nav-link nav-sub-link {{ request()->routeIs('vc.dashboard') ? 'active' : '' }}" href="{{ route('vc.dashboard') }}">
                        <i class="ti ti-dashboard"></i>
                        <span>VC Dashboard</span>
                    </a>
                </div>
            </div>
            @endrole
            
            <!-- 9. Administration Section -->
            @hasanyrole('admin|hr-officer|hod|dean|dvca|dvc-pfa|vice-chancellor')
            <div class="nav-section">
                <div class="nav-section-header" data-section="admin">
                    <div class="nav-section-title">
                        <i class="ti ti-settings"></i>
                        <span>Administration</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="admin">
                    
                    @role('admin')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.roles*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                        <i class="ti ti-lock"></i>
                        <span>Role Management</span>
                    </a>
                    @endrole
                    
                    @role('admin')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.acting-dvc*') ? 'active' : '' }}" href="{{ route('admin.acting-dvc.index') }}">
                        <i class="ti ti-user-check"></i>
                        <span>Acting DVC</span>
                    </a>
                    @endrole
                    
                    <a class="nav-link nav-sub-link {{ request()->routeIs('audit-logs*') ? 'active' : '' }}" href="{{ route('audit-logs.index') }}">
                        <i class="ti ti-history"></i>
                        <span>Audit Logs</span>
                    </a>
                    
                    @hasanyrole('admin|hr-officer')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('reports*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                        <i class="ti ti-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                    @endhasanyrole
                    
                </div>
            </div>
            @endhasanyrole
            
            <!-- 10. Settings Section -->
            <div class="nav-section">
                <div class="nav-section-header" data-section="settings">
                    <div class="nav-section-title">
                        <i class="ti ti-settings"></i>
                        <span>Settings</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="settings">
                    
                    @php
                        $user = auth()->user();
                        $role = $user->getRoleNames()->first();
                    @endphp
                    
                    <!-- Admin: Full System Settings -->
                    @role('admin')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('settings.index') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                        <i class="ti ti-settings"></i>
                        <span>System Settings</span>
                    </a>
                    @endrole
                    
                    <!-- Super Admin: Multi-tenant Settings -->
                    @role('super-admin')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.tenants*') ? 'active' : '' }}" href="{{ route('admin.tenants.index') }}">
                        <i class="ti ti-building"></i>
                        <span>Tenant Management</span>
                    </a>
                    @endrole
                    
                    <!-- HR Officer: HR Settings -->
                    @role('hr-officer')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('settings.hr') ? 'active' : '' }}" href="{{ route('settings.hr') }}">
                        <i class="ti ti-briefcase"></i>
                        <span>HR Settings</span>
                    </a>
                    <a class="nav-link nav-sub-link {{ request()->routeIs('settings.policies') ? 'active' : '' }}" href="{{ route('settings.policies') }}">
                        <i class="ti ti-file-text"></i>
                        <span>Leave Policies</span>
                    </a>
                    @endrole
                    
                    <!-- Database Schema Viewer - Admin only -->
                    @role('admin')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.database-schema*') ? 'active' : '' }}" href="{{ route('admin.database-schema.index') }}">
                        <i class="ti ti-database"></i>
                        <span>Database Schema</span>
                    </a>
                    @endrole
                    
                    <!-- All Staff: My Preferences -->
                    @hasanyrole('staff|hod|dean|hr-officer|dvca|dvc-pfa|deputy-hod|dept-secretary|vice-chancellor')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('profile.settings') ? 'active' : '' }}" href="{{ route('profile.settings') }}">
                        <i class="ti ti-user"></i>
                        <span>My Preferences</span>
                    </a>
                    <a class="nav-link nav-sub-link {{ request()->routeIs('profile.notifications') ? 'active' : '' }}" href="{{ route('profile.notifications') }}">
                        <i class="ti ti-bell"></i>
                        <span>Notification Settings</span>
                    </a>
                    @endhasanyrole
                    
                </div>
            </div>
            
        </div>
    </nav>
</div>

<!-- Sidebar Footer -->
<div class="sidebar-fixed-footer">
    <x-sidebar-user />
    <x-sidebar-footer />
</div>

<style>
/* ============================================
   SIDEBAR REDESIGN - #fdf8f4 Background
   ============================================ */

/* Update sidebar background */
.sidebar-area {
    background-color: #fdf8f4 !important;
}

/* Update sidebar footer */
.sidebar-fixed-footer {
    background: #fdf8f4 !important;
    border-top: 1px solid #E8E2D5;
}

/* Update sidebar navigation background */
.sidebar-nav-scroll {
    background-color: #fdf8f4;
}

/* ============================================
   REDUCED SPACING - Compact Menu
   ============================================ */

/* Reduce gap between nav items */
.sidebar-nav .nav {
    display: flex;
    flex-direction: column;
    padding: 0 0.4rem;
    gap: 0.05rem;
}

/* Reduce section header padding */
.nav-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.2rem 0.4rem;
    color: #475569;
    border-radius: 0 !important;
    transition: all 0.15s;
    font-size: 0.78rem;
    font-weight: 500;
    cursor: pointer;
}

/* HOVER COLOR FOR MAIN MENU (Section Headers) */
.nav-section-header:hover {
    background: #F2D2BD !important;
    color: #1E293B !important;
}

.nav-section-header:hover i {
    color: #1E293B !important;
}

/* Reduce submenu padding */
.nav-sub-link {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.15rem 0.4rem !important;
    color: #475569;
    border-radius: 0 !important;
    transition: all 0.15s;
    font-size: 0.72rem;
    text-decoration: none;
}

/* INCREASED SUBMENU INDENTATION - Changed from 0.6rem to 1.2rem */
.nav-section-content {
    display: none;
    padding-left: 1.2rem;
    margin-top: 0.03rem;
    margin-bottom: 0.03rem;
}

/* Reduce regular nav link padding */
.sidebar-nav .nav-link {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.2rem 0.4rem;
    color: #475569;
    border-radius: 0 !important;
    transition: all 0.15s;
    font-size: 0.78rem;
    font-weight: 500;
    text-decoration: none;
}

/* Smaller icons */
.nav-section-title i,
.sidebar-nav .nav-link i {
    width: 16px;
    font-size: 0.85rem;
    color: #94A3B8;
}

.nav-sub-link i {
    width: 16px;
    font-size: 0.72rem;
    color: #94A3B8;
}

/* ============================================
   SEPARATOR LINES - REMOVED
   ============================================ */

/* REMOVED: Separator between main menu items */
/* .nav-section:not(:last-child)::after { ... } */

/* REMOVED: Separator between sub-menu items */
/* .nav-sub-link:not(:last-child)::after { ... } */

/* ============================================
   NO ROUNDED CORNERS ON HOVER
   ============================================ */

.nav-sub-link:hover,
.nav-sub-link.active,
.nav-section-header:hover,
.sidebar-nav .nav-link:hover,
.sidebar-nav .nav-link.active {
    border-radius: 0 !important;
}

.nav-section-header {
    border-radius: 0 !important;
}

/* ============================================
   HOVER & ACTIVE STATES
   ============================================ */

.nav-sub-link:hover {
    background: #F2D2BD !important;
    color: #1E293B !important;
}

.nav-sub-link:hover i {
    color: #1E293B !important;
}

.nav-sub-link.active {
    background: #EECBC4 !important;
    color: #1E293B !important;
}

.nav-sub-link.active i {
    color: #1E293B !important;
}

.sidebar-nav .nav-link:hover {
    background: #F2D2BD !important;
    color: #1E293B !important;
}

.sidebar-nav .nav-link:hover i {
    color: #1E293B !important;
}

.sidebar-nav .nav-link.active {
    background: #EECBC4 !important;
    color: #1E293B !important;
}

.sidebar-nav .nav-link.active i {
    color: #1E293B !important;
}

/* ============================================
   SIDEBAR CONTAINER & SCROLL
   ============================================ */

.sidebar-nav-container {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-height: 0;
    overflow: hidden;
}

.sidebar-nav-scroll {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
}

.sidebar-nav-scroll::-webkit-scrollbar {
    width: 3px;
}

.sidebar-nav-scroll::-webkit-scrollbar-track {
    background: #E8E2D5;
}

.sidebar-nav-scroll::-webkit-scrollbar-thumb {
    background: #CBD5E1;
    border-radius: 3px;
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
}

.nav-section {
    margin-bottom: 0;
}

.nav-section-title {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.section-arrow {
    font-size: 0.6rem;
    transition: transform 0.2s ease;
    color: #94A3B8;
}

.nav-section-header.open .section-arrow {
    transform: rotate(90deg);
}

.nav-section-content.show {
    display: block;
}

/* ============================================
   SIDEBAR COLLAPSED STATE
   ============================================ */

.sidebar-area.collapsed .sidebar-nav-scroll {
    overflow-y: visible;
}

.sidebar-area.collapsed .sidebar-nav .nav-section-title span,
.sidebar-area.collapsed .section-arrow,
.sidebar-area.collapsed .nav-sub-link span {
    display: none;
}

.sidebar-area.collapsed .sidebar-fixed-footer {
    padding: 0.5rem 0;
}

.sidebar-area.collapsed .nav-section-header {
    justify-content: center;
    padding: 0.2rem 0;
}

.sidebar-area.collapsed .nav-section-title {
    justify-content: center;
}

.sidebar-area.collapsed .nav-section-title i {
    margin-right: 0;
}

.sidebar-area.collapsed .nav-sub-link {
    justify-content: center;
    padding: 0.15rem 0 !important;
}

.sidebar-area.collapsed .nav-section-content {
    position: absolute;
    left: 70px;
    background: #fdf8f4 !important;
    border: 1px solid #E8E2D5;
    border-radius: 0 !important;
    padding: 0.15rem;
    min-width: 160px;
    z-index: 1000;
}

.sidebar-area.collapsed .nav-section-content.show {
    display: block;
}

.sidebar-area.collapsed .nav-section-content .nav-sub-link span {
    display: inline;
}
</style>

<script>
// Collapsible Sidebar Sections
document.addEventListener('DOMContentLoaded', function() {
    const sections = ['overview', 'institute', 'hr', 'leave', 'messages', 'permission', 'central-registry', 'vc', 'admin', 'settings'];
    const sidebarArea = document.getElementById('sidebarArea');
    
    function closeAllSections() {
        sections.forEach(section => {
            const header = document.querySelector(`.nav-section-header[data-section="${section}"]`);
            const content = document.querySelector(`.nav-section-content[data-section-content="${section}"]`);
            if (header && content) {
                header.classList.remove('open');
                content.classList.remove('show');
            }
        });
    }
    
    function openSection(section) {
        const header = document.querySelector(`.nav-section-header[data-section="${section}"]`);
        const content = document.querySelector(`.nav-section-content[data-section-content="${section}"]`);
        if (header && content) {
            header.classList.add('open');
            content.classList.add('show');
        }
    }
    
    sections.forEach(section => {
        const header = document.querySelector(`.nav-section-header[data-section="${section}"]`);
        if (header) {
            header.addEventListener('click', function(e) {
                e.stopPropagation();
                const content = document.querySelector(`.nav-section-content[data-section-content="${section}"]`);
                const isOpen = content.classList.contains('show');
                
                if (!sidebarArea.classList.contains('collapsed')) {
                    closeAllSections();
                    if (!isOpen) {
                        openSection(section);
                    }
                }
            });
        }
    });
    
    const submenuLinks = document.querySelectorAll('.nav-sub-link');
    submenuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-link:not(.nav-sub-link)');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    const isDashboard = window.location.pathname === '/dashboard' || window.location.pathname.includes('/dashboard');
    
    if (!sidebarArea.classList.contains('collapsed') && isDashboard) {
        openSection('overview');
    }
    
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                const isCollapsed = sidebarArea.classList.contains('collapsed');
                if (isCollapsed) {
                    closeAllSections();
                } else if (isDashboard) {
                    openSection('overview');
                }
            }
        });
    });
    
    observer.observe(sidebarArea, { attributes: true });
});

// Fetch unread message count for sidebar badge
function fetchUnreadCount() {
    fetch('{{ route("messages.unread-count") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('unreadMessagesBadge');
            if (badge) {
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error fetching unread count:', error));
}

// Fetch pending access requests count (for VC)
function fetchPendingRequestsCount() {
    fetch('/messages/access-requests/count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('pendingRequestsBadge');
            if (badge) {
                if (data.count && data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error fetching pending requests:', error));
}

// Fetch immediately on page load
document.addEventListener('DOMContentLoaded', function() {
    fetchUnreadCount();
    fetchPendingRequestsCount();
    
    // Fetch every 30 seconds (for real-time updates)
    setInterval(fetchUnreadCount, 30000);
    setInterval(fetchPendingRequestsCount, 30000);
});
</script>