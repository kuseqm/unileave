@php
    $user = Auth::user();
    $role = $user->getRoleNames()->first() ?? 'Staff';

    // Only the true cross-institute super-admin has no single institute to
    // show - every institute-scoped admin (and everyone else) should see
    // their real institute name here, not the generic app name.
    if ($user->hasRole('super-admin')) {
        $displayUniversity = 'Staff Management System';
    } else {
        $displayUniversity = $user->university->name ?? 'Not Assigned';
    }
@endphp

<div class="sidebar-user">
    <div class="sidebar-university-icon">
        <i class="ti ti-building"></i>
    </div>
    <div class="sidebar-user-details">
        <div class="sidebar-user-university" style="color: #3B82F6; font-weight: 600;">
            {{ $displayUniversity }}
        </div>
        <div class="sidebar-user-role">
            <span class="role-badge">{{ ucfirst($role) }}</span>
        </div>
    </div>
</div>

<style>
.sidebar-user {
    display: flex;
    gap: 0.75rem;
    padding: 0.6rem 0.5rem;
    margin-bottom: 0.5rem;
    background: transparent;
    align-items: center;
}

.sidebar-university-icon {
    width: 36px;
    height: 36px;
    background: #EFF6FF;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #3B82F6;
    font-size: 1rem;
    flex-shrink: 0;
}

.sidebar-user-details {
    flex: 1;
}

.sidebar-user-university {
    font-size: 0.7rem;
    font-weight: 600;
    color: #3B82F6;
    margin-bottom: 0.25rem;
    line-height: 1.3;
}

.sidebar-user-role {
    display: inline-block;
}

.role-badge {
    font-size: 0.55rem;
    background: #DBEAFE;
    color: #3B82F6;
    padding: 0.15rem 0.5rem;
    border-radius: 0.25rem;
}

/* When sidebar collapsed */
.sidebar-area.collapsed .sidebar-user {
    justify-content: center;
    padding: 0.6rem 0;
}

.sidebar-area.collapsed .sidebar-user-details {
    display: none;
}

.sidebar-area.collapsed .sidebar-university-icon {
    margin: 0;
}
</style>