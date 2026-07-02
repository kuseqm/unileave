<div class="sidebar-footer">
    <div class="sidebar-copyright">
        <i class="ti ti-copyright" style="font-size: 0.65rem;"></i>
        <span>{{ date('Y') }} UniLeave. All rights reserved.</span>
    </div>
    <div class="sidebar-version">
        <span>v1.0.0</span>
    </div>
</div>

<style>
.sidebar-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0.75rem;
    background: #E8E2D5;
    border-radius: 0.5rem;
    margin-top: 0.5rem;
}

.sidebar-copyright {
    font-size: 0.6rem;
    color: #475569;
}

.sidebar-copyright i {
    font-size: 0.65rem;
}

.sidebar-version {
    font-size: 0.55rem;
    color: #94A3B8;
}

/* When sidebar collapsed */
.sidebar-area.collapsed .sidebar-footer {
    justify-content: center;
    padding: 0.5rem 0;
}

.sidebar-area.collapsed .sidebar-copyright span,
.sidebar-area.collapsed .sidebar-version {
    display: none;
}

.sidebar-area.collapsed .sidebar-copyright i {
    margin: 0;
}
</style>