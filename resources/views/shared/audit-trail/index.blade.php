@extends('layouts.master')

@section('title', 'Audit Trail Dashboard')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Page Header -->
    <x-page-header title="AUDIT TRAIL DASHBOARD" description="Complete system activity log for compliance and investigation">
        <div class="d-flex gap-2">
            <a href="{{ route('audit-trail.export', request()->query()) }}" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-download me-1"></i> Export CSV
            </a>
            <a href="{{ route('audit-trail.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-refresh me-1"></i> Refresh
            </a>
        </div>
    </x-page-header>

    <!-- Stats Cards Row - Following your exact pattern -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ number_format($stats['total']) }}" label="Total Events" subtext="all time recorded" />
        <x-stats-card value="{{ number_format($stats['today']) }}" label="Today" subtext="events in last 24 hours" color="info" />
        <x-stats-card value="{{ number_format($stats['this_week']) }}" label="This Week" subtext="last 7 days" color="success" />
        <x-stats-card value="{{ number_format($stats['this_month']) }}" label="This Month" subtext="current month" color="warning" />
    </div>

    <!-- Event Distribution Row -->
    <div class="row g-2 mb-3">
        <div class="col-md-6">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-chart-pie me-1"></i> Events by Type
                </div>
                <div class="card-body p-2">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($stats['by_event'] as $event)
                            <span class="badge bg-secondary">
                                {{ ucfirst($event->event) }}: {{ number_format($event->total) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-database me-1"></i> Entities Monitored
                </div>
                <div class="card-body p-2">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($stats['by_type'] as $type)
                            <span class="badge bg-info">
                                {{ str_replace('App\Models\\', '', $type->auditable_type) }}: {{ number_format($type->total) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card rounded-sm mb-3">
        <div class="card-body py-2 px-3">
            <form method="GET" action="{{ route('audit-trail.index') }}" class="row g-2">
                <div class="col-md-2">
                    <select name="entity_type" class="form-select form-select-sm">
                        <option value="all">All Entities</option>
                        <option value="message" {{ request('entity_type') == 'message' ? 'selected' : '' }}>Messages</option>
                        <option value="leave" {{ request('entity_type') == 'leave' ? 'selected' : '' }}>Leave Requests</option>
                        <option value="permission" {{ request('entity_type') == 'permission' ? 'selected' : '' }}>Permissions</option>
                        <option value="travel" {{ request('entity_type') == 'travel' ? 'selected' : '' }}>Travel</option>
                        <option value="letter" {{ request('entity_type') == 'letter' ? 'selected' : '' }}>Letters</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="event" class="form-select form-select-sm">
                        <option value="all">All Events</option>
                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        <option value="restored" {{ request('event') == 'restored' ? 'selected' : '' }}>Restored</option>
                        <option value="viewed" {{ request('event') == 'viewed' ? 'selected' : '' }}>Viewed</option>
                        <option value="approved" {{ request('event') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('event') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="accessed_via_code" {{ request('event') == 'accessed_via_code' ? 'selected' : '' }}>Secure Access</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="all">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->first_name }} {{ $user->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="Date From">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="Date To">
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100" style="font-size: 0.7rem;">
                            <i class="ti ti-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('audit-trail.index') }}" class="btn btn-secondary btn-sm" style="font-size: 0.7rem;">
                            <i class="ti ti-clear"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Trail Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-history text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Activity Log</span>
                    <span class="text-muted ms-2 small">Showing {{ $audits->firstItem() ?? 0 }} to {{ $audits->lastItem() ?? 0 }} of {{ $audits->total() }}</span>
                </div>
                <div class="text-muted small">
                    <i class="ti ti-shield me-1"></i> Non-repudiation record
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;" id="auditTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">#</th>
                            <th class="py-1">Date/Time</th>
                            <th class="py-1">User</th>
                            <th class="py-1">Role</th>
                            <th class="py-1">Entity</th>
                            <th class="py-1">Event</th>
                            <th class="py-1">Action</th>
                            <th class="py-1">IP Address</th>
                            <th class="pe-2 py-1" width="50">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                        <tr>
                            <td class="ps-2 py-1">{{ $audit->id }}</td>
                            <td class="py-1">
                                {{ $audit->created_at->format('d M Y, H:i:s') }}
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $audit->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-1">
                                {{ $audit->user->first_name ?? 'System' }} {{ $audit->user->last_name ?? '' }}
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $audit->user->email ?? '' }}</div>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-secondary rounded-sm" style="font-size: 0.6rem;">{{ $audit->user_role ?? 'Unknown' }}</span>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-info rounded-sm" style="font-size: 0.6rem;">{{ $audit->entity_type }}</span>
                                <div class="text-muted" style="font-size: 0.6rem;">ID: {{ $audit->auditable_id }}</div>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-{{ $audit->event == 'created' ? 'success' : ($audit->event == 'deleted' ? 'danger' : ($audit->event == 'accessed_via_code' ? 'warning' : 'secondary')) }} rounded-sm" style="font-size: 0.6rem;">
                                    {{ ucfirst($audit->event) }}
                                </span>
                            </td>
                            <td class="py-1">{{ Str::limit($audit->action, 50) }}</td>
                            <td class="py-1"><code style="font-size: 0.6rem;">{{ $audit->ip_address ?? 'N/A' }}</code></td>
                            <td class="pe-2 py-1">
                                <a href="{{ route('audit-trail.show', $audit->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="View Details">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                <i class="ti ti-history" style="font-size: 48px;"></i>
                                <p class="mt-2">No audit records found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <x-pagination :items="$audits" :perPageOptions="[10, 25, 50, 100]" />
        </div>
    </div>
</div>
@endsection