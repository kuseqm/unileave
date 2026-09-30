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
            
            <!-- 2. Institute Section -->
            @canany(['manage_universities','manage_campuses','manage_schools','manage_departments'])
            <div class="nav-section">
                <div class="nav-section-header" data-section="institute">
                    <div class="nav-section-title">
                        <i class="ti ti-building"></i>
                        <span>Institute</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="institute">
                    @can('manage_universities')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.universities*') ? 'active' : '' }}" href="{{ route('admin.universities.index') }}">
                        <i class="ti ti-building-community"></i>
                        <span>Universities</span>
                    </a>
                    @endcan
                    @can('manage_campuses')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.campuses*') ? 'active' : '' }}" href="{{ route('admin.campuses.index') }}">
                        <i class="ti ti-map-pin"></i>
                        <span>Campuses</span>
                    </a>
                    @endcan
                    @can('manage_schools')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.schools*') ? 'active' : '' }}" href="{{ route('admin.schools.index') }}">
                        <i class="ti ti-school"></i>
                        <span>Schools/Faculties</span>
                    </a>
                    @endcan
                    @can('manage_departments')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.departments*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">
                        <i class="ti ti-folder"></i>
                        <span>Departments</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany
            
            <!-- 3. Human Resources Section -->
            @can('view_staff')
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
                    
                    @can('import_staff')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.import.staff') ? 'active' : '' }}" href="{{ route('admin.import.staff') }}">
                        <i class="ti ti-file-import"></i>
                        <span>Import Staff</span>
                    </a>
                    @endcan

                    @haspermission('view_id_cards')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.id-cards*') ? 'active' : '' }}" href="{{ route('admin.id-cards.index') }}">
                        <i class="ti ti-id"></i>
                        <span>ID Cards</span>
                    </a>
                    @endhaspermission

                    @haspermission('view_authorized_signatures')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.authorized-signatures*') ? 'active' : '' }}" href="{{ route('admin.authorized-signatures.index') }}">
                        <i class="ti ti-signature"></i>
                        <span>Authorized Signatures</span>
                    </a>
                    @endhaspermission
                </div>
            </div>
            @endcan
            
            <!-- 4. Leave Management Section -->
            @canany(['apply_leave','view_own_leave','view_all_leave','view_school_leave','view_department_leave','view_pending_approvals','admin_access','manage_blackout_periods','request_acting_staff','view_acting_assignments','view_acting_requests'])
            <div class="nav-section">
                <div class="nav-section-header" data-section="leave">
                    <div class="nav-section-title">
                        <i class="ti ti-calendar"></i>
                        <span>Leave Management</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="leave">
                    @can('apply_leave')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.create') ? 'active' : '' }}" href="{{ route('leave-requests.create') }}">
                        <i class="ti ti-plus"></i>
                        <span>Apply for Leave</span>
                    </a>
                    @endcan
                    
                    @canany(['view_own_leave','view_all_leave'])
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.my-requests') ? 'active' : '' }}" href="{{ route('leave-requests.my-requests') }}">
                        <i class="ti ti-list"></i>
                        <span>My Requests</span>
                    </a>
                    @endcanany
                    
                    @canany(['view_own_leave','view_all_leave'])
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.status') ? 'active' : '' }}" href="{{ route('leave-requests.status') }}">
                        <i class="ti ti-list-details"></i>
                        <span>Request Status</span>
                    </a>
                    @endcanany

                    @canany(['view_own_leave','view_all_leave','view_department_leave','view_school_leave'])
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-letters.*') ? 'active' : '' }}" href="{{ route('leave-letters.index') }}">
                        <i class="ti ti-file-text"></i>
                        <span>My Leave Letters</span>
                    </a>
                    @endcanany

                    @can('submit_leave_on_behalf')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.on-behalf') ? 'active' : '' }}" href="{{ route('leave-requests.on-behalf') }}">
                        <i class="ti ti-user-plus"></i>
                        <span>Submit on Behalf</span>
                    </a>
                    @endcan

                    @canany(['track_department_leave','view_department_leave'])
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.department-leave.*') ? 'active' : '' }}" href="{{ route('admin.department-leave.index') }}">
                        <i class="ti ti-users-group"></i>
                        <span>Track Department Leave</span>
                    </a>
                    @endcanany

                    @can('prepare_leave_documents')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.leave-documents.*') ? 'active' : '' }}" href="{{ route('admin.leave-documents.index') }}">
                        <i class="ti ti-file-description"></i>
                        <span>Prepare Leave Documents</span>
                    </a>
                    @endcan

                    <!-- WAITING APPROVALS - Permission-based counter -->
                    @can('view_pending_approvals')
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
                    @endcan

                    @hasanyrole('hod|dean|unit_head|hr-officer|hr-manager|dvca|dvc-pfa|admin')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.unresponsive-acting') ? 'active' : '' }}" href="{{ route('leave-requests.unresponsive-acting') }}">
                        <i class="ti ti-alert-circle"></i>
                        <span>Unresponsive Acting Staff</span>
                        @php
                            // Same scope the "Unresponsive Acting Staff" page and dashboard widget
                            // use, so this badge can never drift from what that page actually shows.
                            $unresponsiveNavCount = \App\Models\LeaveRequest::actingOverdueVisible()->count();
                        @endphp
                        @if($unresponsiveNavCount > 0)
                        <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.55rem; padding: 0.2rem 0.5rem;">
                            {{ $unresponsiveNavCount > 99 ? '99+' : $unresponsiveNavCount }}
                        </span>
                        @endif
                    </a>
                    @endhasanyrole

                    @can('manage_leave_types')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.leave-types*') ? 'active' : '' }}" href="{{ route('admin.leave-types.index') }}">
                        <i class="ti ti-tags"></i>
                        <span>Leave Types</span>
                    </a>
                    @endcan
                    
                    @can('manage_blackout_periods')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('blackout.*') ? 'active' : '' }}" href="{{ route('blackout.index') }}">
                        <i class="ti ti-ban"></i>
                        <span>Blackout Periods</span>
                    </a>
                    @endcan

                    @canany(['request_acting_staff','view_acting_assignments','view_acting_requests'])
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.hold-position-for-me') ? 'active' : '' }}" href="{{ route('leave-requests.hold-position-for-me') }}">
                        <i class="ti ti-clock-cog"></i>
                        <span>Acting Requests</span>
                    </a>
                    @endcanany
                    
                    @canany(['view_acting_assignments','view_acting_requests','request_acting_staff'])
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.i-held-position-for') ? 'active' : '' }}" href="{{ route('leave-requests.i-held-position-for') }}">
                        <i class="ti ti-user-check"></i>
                        <span>My Assignments</span>
                    </a>
                    @endcanany

                    @canany(['view_all_leave','view_school_leave','view_department_leave','view_pending_approvals'])
                    <a class="nav-link nav-sub-link {{ request()->routeIs('leave-requests.approvals-history') ? 'active' : '' }}" href="{{ route('leave-requests.approvals-history') }}">
                        <i class="ti ti-history"></i>
                        <span>My Approvals History</span>
                    </a>
                    @endcanany
                </div>
            </div>
            @endcanany
            
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

                    <a class="nav-link nav-sub-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                        <i class="ti ti-bell"></i>
                        <span>Notifications</span>
                    </a>

                    <!-- Divider -->
                    <div class="dropdown-divider my-1"></div>
                    
                    <!-- Message Management Section (Admin/HR/HOD/School Admin/VC) -->
                    @php
                        $user = auth()->user();
                        $role = $user->getRoleNames()->first();
                    @endphp
                    
                    @hasanyrole('admin|hr-manager|vice-chancellor|school-admin|hod')
                        <div class="fw-semibold small text-muted mt-2 mb-1 px-2">
                            <i class="ti ti-database me-1" style="font-size: 0.7rem;"></i> Management
                        </div>
                        
                        <!-- Message Directory -->
                        <a class="nav-link nav-sub-link {{ request()->routeIs('messages.admin-list*') ? 'active' : '' }}" href="{{ route('messages.admin-list') }}">
                            <i class="ti ti-database"></i>
                            <span>Message Directory</span>
                        </a>
                    @endhasanyrole
                    
                    <!-- VC: Access Requests -->
                    @hasanyrole('vice-chancellor|admin')
                        <a class="nav-link nav-sub-link {{ request()->routeIs('messages.access-requests*') ? 'active' : '' }}" href="{{ route('messages.access-requests') }}">
                            <i class="ti ti-key"></i>
                            <span>Access Requests</span>
                            <span class="badge bg-warning rounded-pill ms-1" id="pendingRequestsBadge" style="font-size: 0.55rem; padding: 0.1rem 0.4rem; display: none;">0</span>
                        </a>
                    @endhasanyrole
                    
                </div>
            </div>
            @endauth

            <!-- 5b. Oversight Section (Audit Trail: view_audit_trail; Applications Overview: view_all_leave|view_pending_approvals) -->
            @canany(['view_audit_trail','view_all_leave','view_pending_approvals'])
            <div class="nav-section">
                <div class="nav-section-header" data-section="oversight">
                    <div class="nav-section-title">
                        <i class="ti ti-shield-check"></i>
                        <span>Oversight</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="oversight">
                    @can('view_audit_trail')
                    @php
                        $auditCanViewAll = auth()->user()->hasAnyRole(['admin', 'hr-manager', 'vice-chancellor', 'internal-auditor', 'registrar']);
                        $recentAudits = $auditCanViewAll
                            ? \App\Models\UnifiedAuditTrail::whereDate('created_at', today())->count()
                            : \App\Models\UnifiedAuditTrail::where('user_id', auth()->id())->whereDate('created_at', today())->count();
                    @endphp
                    <a class="nav-link nav-sub-link {{ request()->routeIs('audit-trail*') ? 'active' : '' }}" href="{{ route('audit-trail.index') }}">
                        <i class="ti ti-history"></i>
                        <span>Audit Trail{{ $auditCanViewAll ? ' Dashboard' : '' }}</span>
                        @if($recentAudits > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ $recentAudits > 99 ? '99+' : $recentAudits }}</span>
                        @endif
                    </a>
                    @endcan

                    @can('view_audit_trail')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('verification-logs*') ? 'active' : '' }}" href="{{ route('verification-logs.index') }}">
                        <i class="ti ti-scan"></i>
                        <span>Verification Log</span>
                    </a>
                    @endcan

                    @canany(['view_all_leave','view_pending_approvals'])
                        <a class="nav-link nav-sub-link {{ request()->routeIs('applications-overview*') ? 'active' : '' }}" href="{{ route('applications-overview.index') }}">
                            <i class="ti ti-list-details"></i>
                            <span>Applications Overview</span>
                        </a>
                    @endcanany
                </div>
            </div>
            @endcanany

            <!-- 6. Permission Management Section -->
            @canany(['apply_leave','view_own_leave','view_all_leave','view_school_leave','view_department_leave','view_pending_approvals'])
            <div class="nav-section">
                <div class="nav-section-header" data-section="permission">
                    <div class="nav-section-title">
                        <i class="ti ti-file-text"></i>
                        <span>Permission Management</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="permission">
                    @can('apply_leave')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('permissions.create') ? 'active' : '' }}" href="{{ route('permissions.create') }}">
                        <i class="ti ti-clock"></i>
                        <span>Apply for Permission</span>
                    </a>
                    @endcan
                    
                    @can('apply_leave')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('travel-permissions.create') ? 'active' : '' }}" href="{{ route('travel-permissions.create') }}">
                        <i class="ti ti-plane"></i>
                        <span>Apply for Travel</span>
                    </a>
                    @endcan
                    
                    @canany(['view_own_leave','view_all_leave'])
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
                    @endcanany
                    
                    @canany(['view_pending_approvals','view_all_leave'])
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
                    @endcanany

                    @canany(['view_all_leave','view_school_leave','view_department_leave','view_own_leave'])
                    <a class="nav-link nav-sub-link {{ request()->routeIs('permissions.approvals-history') ? 'active' : '' }}" href="{{ route('permissions.approvals-history') }}">
                        <i class="ti ti-history"></i>
                        <span>Permission History</span>
                    </a>
                    <a class="nav-link nav-sub-link {{ request()->routeIs('travel-permissions.approvals-history') ? 'active' : '' }}" href="{{ route('travel-permissions.approvals-history') }}">
                        <i class="ti ti-history"></i>
                        <span>Travel History</span>
                    </a>
                    @endcanany
                </div>
            </div>
            @endcanany

            <!-- 7. Central Registry Section (manage_registry) -->
            @can('manage_registry')
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
                    
                    <!-- Registry Settings (manage_registry) -->
                    @can('manage_registry')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('registry.offices*') ? 'active' : '' }}" href="{{ route('registry.offices.index') }}">
                        <i class="ti ti-building"></i>
                        <span>Manage Offices</span>
                    </a>
                    
                    <a class="nav-link nav-sub-link {{ request()->routeIs('registry.settings') ? 'active' : '' }}" href="{{ route('registry.settings') }}">
                        <i class="ti ti-settings"></i>
                        <span>Registry Settings</span>
                    </a>

                    <a class="nav-link nav-sub-link {{ request()->routeIs('registry.formats*') ? 'active' : '' }}" href="{{ route('registry.formats.index') }}">
                        <i class="ti ti-hash"></i>
                        <span>Reference Formats</span>
                    </a>
                    @endcan

                </div>
            </div>
            @endcan

            <!-- 7b. Academic Records (Students / Graduation Verification) -
                 deliberately its own section, not nested under Central
                 Registry, even though it shares that module's reference
                 numbers - a Student is never a Staff. Gated by
                 view_students (super-admin/admin/registrar have it by
                 default - see RoleAndPermissionSeeder) AND by the
                 institution having the "academic_records" add-on enabled
                 (see EnsureAddonEnabled) - a role having the permission
                 isn't enough if a super-admin hasn't granted the
                 institution the add-on itself. -->
            @can('view_students')
            @if(auth()->user()->hasAddon('academic_records'))
            <div class="nav-section">
                <div class="nav-section-header" data-section="academic-records">
                    <div class="nav-section-title">
                        <i class="ti ti-school"></i>
                        <span>Academic Records</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="academic-records">
                    <a class="nav-link nav-sub-link {{ request()->routeIs('students.*') && ! request()->routeIs('students.import.*') && ! request()->routeIs('students.statistics') && ! request()->routeIs('students.catalog.*') ? 'active' : '' }}" href="{{ route('students.index') }}">
                        <i class="ti ti-users"></i>
                        <span>Graduates</span>
                    </a>
                    @can('view_students')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('verification-letters.*') ? 'active' : '' }}" href="{{ route('verification-letters.index') }}">
                        <i class="ti ti-file-certificate"></i>
                        <span>Verification Letters</span>
                    </a>
                    @endcan
                    <a class="nav-link nav-sub-link {{ request()->routeIs('students.statistics') ? 'active' : '' }}" href="{{ route('students.statistics') }}">
                        <i class="ti ti-chart-bar"></i>
                        <span>Graduate Statistics</span>
                    </a>
                    @can('import_students')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('students.import.*') ? 'active' : '' }}" href="{{ route('students.import.index') }}">
                        <i class="ti ti-upload"></i>
                        <span>Import Graduation Roster</span>
                    </a>
                    <a class="nav-link nav-sub-link {{ request()->routeIs('students.catalog.*') ? 'active' : '' }}" href="{{ route('students.catalog.index') }}">
                        <i class="ti ti-category-plus"></i>
                        <span>Academic Catalog</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endif
            @endcan

            <!-- 8. VC Dashboard (view_institution_dashboard) -->
            @can('view_institution_dashboard')
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
            @endcan
            
            <!-- 9. Administration Section -->
            @canany(['admin_access','view_audit_logs','view_reports','view_gate_entry_logs','record_entry_exit','verify_qr_codes'])
            <div class="nav-section">
                <div class="nav-section-header" data-section="admin">
                    <div class="nav-section-title">
                        <i class="ti ti-settings"></i>
                        <span>Administration</span>
                    </div>
                    <i class="ti ti-chevron-right section-arrow"></i>
                </div>
                <div class="nav-section-content" data-section-content="admin">
                    
                    @if(auth()->user()->can('view_roles') && auth()->user()->can('admin_access'))
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.roles*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                        <i class="ti ti-lock"></i>
                        <span>Role Management</span>
                    </a>
                    @endif
                    
                    @canany(['manage_acting_dvc','approve_acting_dvc'])
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.acting-dvc*') ? 'active' : '' }}" href="{{ route('admin.acting-dvc.index') }}">
                        <i class="ti ti-user-check"></i>
                        <span>Acting DVC</span>
                    </a>
                    @endcanany

                    @can('delegate_approval')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.approval-delegations*') ? 'active' : '' }}" href="{{ route('admin.approval-delegations.index') }}">
                        <i class="ti ti-user-share"></i>
                        <span>Approval Delegations</span>
                    </a>
                    @endcan

                    @can('manage_leave_approval_chain')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.leave-approval-chain*') ? 'active' : '' }}" href="{{ route('admin.leave-approval-chain.index') }}">
                        <i class="ti ti-arrows-shuffle"></i>
                        <span>Leave Approval Chain</span>
                    </a>
                    @endcan

                    @can('manage_announcements')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}" href="{{ route('admin.announcements.index') }}">
                        <i class="ti ti-speakerphone"></i>
                        <span>Announcements</span>
                    </a>
                    @endcan

                    @can('manage_events')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.events*') ? 'active' : '' }}" href="{{ route('admin.events.index') }}">
                        <i class="ti ti-calendar-event"></i>
                        <span>Events</span>
                    </a>
                    @endcan

                    @can('manage_team_members')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.team-members*') ? 'active' : '' }}" href="{{ route('admin.team-members.index') }}">
                        <i class="ti ti-users-group"></i>
                        <span>Team Members</span>
                    </a>
                    @endcan

                    @can('manage_academic_years')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.academic-years*') ? 'active' : '' }}" href="{{ route('admin.academic-years.index') }}">
                        <i class="ti ti-calendar-stats"></i>
                        <span>Academic Years</span>
                    </a>
                    @endcan

                    @can('view_audit_logs')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('audit-logs*') ? 'active' : '' }}" href="{{ route('audit-logs.index') }}">
                        <i class="ti ti-history"></i>
                        <span>Audit Logs</span>
                    </a>
                    @endcan
                    
                    @can('view_reports')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('reports*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                        <i class="ti ti-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                    @endcan

                    @canany(['view_gate_entry_logs','record_entry_exit','verify_qr_codes'])
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.gate-entry-logs*') ? 'active' : '' }}" href="{{ route('admin.gate-entry-logs.index') }}">
                        <i class="ti ti-door"></i>
                        <span>Gate Entry Logs</span>
                    </a>
                    @endcanany

                    @can('admin_access')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.cleanup*') ? 'active' : '' }}" href="{{ route('admin.cleanup.index') }}">
                        <i class="ti ti-trash"></i>
                        <span>Bulk Cleanup</span>
                    </a>
                    @endcan

                </div>
            </div>
            @endcanany
            
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

                    <!-- Admin: Full System Settings -->
                    @canany(['view_settings','edit_settings'])
                    <a class="nav-link nav-sub-link {{ request()->routeIs('settings.index') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                        <i class="ti ti-settings"></i>
                        <span>System Settings</span>
                    </a>
                    @endcanany
                    
                    <!-- Super Admin: Multi-tenant Settings -->
                    @can('manage_tenants')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.tenants*') ? 'active' : '' }}" href="{{ route('admin.tenants.index') }}">
                        <i class="ti ti-building"></i>
                        <span>Tenant Management</span>
                    </a>
                    @endcan
                    
                    <!-- HR Officer: HR Settings -->
                    @can('view_settings')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('settings.hr') ? 'active' : '' }}" href="{{ route('settings.hr') }}">
                        <i class="ti ti-briefcase"></i>
                        <span>HR Settings</span>
                    </a>
                    @endcan

                    <!-- University Policies -->
                    @can('view_policies')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('policies*') && ! request()->routeIs('policies.settings') ? 'active' : '' }}" href="{{ route('policies.index') }}">
                        <i class="ti ti-book"></i>
                        <span>University Policies</span>
                    </a>
                    <a class="nav-link nav-sub-link {{ request()->routeIs('policies.settings') ? 'active' : '' }}" href="{{ route('policies.settings') }}">
                        <i class="ti ti-adjustments"></i>
                        <span>Policy Settings</span>
                    </a>
                    @endcan
                    
                    <!-- Database Schema Viewer - Admin only -->
                    @can('admin_access')
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.database-schema*') ? 'active' : '' }}" href="{{ route('admin.database-schema.index') }}">
                        <i class="ti ti-database"></i>
                        <span>Database Schema</span>
                    </a>
                    
                    <!-- Admin: Wipe demo/test requests by date/time -->
                    <a class="nav-link nav-sub-link {{ request()->routeIs('admin.cleanup*') ? 'active' : '' }}" href="{{ route('admin.cleanup.index') }}">
                        <i class="ti ti-trash"></i>
                        <span>Bulk Cleanup</span>
                    </a>
                    @endcan
                    
                    <!-- All Staff: My Preferences -->
                    @auth
                    <a class="nav-link nav-sub-link {{ request()->routeIs('profile.settings') ? 'active' : '' }}" href="{{ route('profile.settings') }}">
                        <i class="ti ti-user"></i>
                        <span>My Preferences</span>
                    </a>
                    <a class="nav-link nav-sub-link {{ request()->routeIs('profile.notifications') ? 'active' : '' }}" href="{{ route('profile.notifications') }}">
                        <i class="ti ti-bell"></i>
                        <span>Notification Settings</span>
                    </a>
                    @endauth
                    
                </div>
            </div>
            
        </div>
    </nav>
</div>

<!-- Sidebar Footer -->
<div class="sidebar-fixed-footer">
    @auth
    <x-sidebar-user />
    @endauth
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
    const sections = ['overview', 'institute', 'hr', 'leave', 'messages', 'oversight', 'permission', 'central-registry', 'academic-records', 'vc', 'admin', 'settings'];
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

// The #unreadMessagesBadge element above is now filled by
// loadUnreadMessageCount() in components/topbar.blade.php - that function
// updates both the topbar's own badge and this sidebar badge from a single
// fetch, instead of this file independently polling the same
// /messages/unread-count endpoint on its own 30s timer while topbar polled
// it on a separate 15s timer.

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
    fetchPendingRequestsCount();

    // Fetch every 30 seconds (for real-time updates)
    setInterval(fetchPendingRequestsCount, 30000);
});
</script>