@extends('layouts.master')

@section('title', 'Audit Trail Dashboard')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Page Header -->
    <x-page-header title="AUDIT TRAIL DASHBOARD" description="{{ $canViewAll ? 'Complete system activity log for compliance and investigation' : 'Your own activity log' }}">
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
        <x-stats-card value="{{ number_format($stats['total']) }}" label="Total Events" subtext="all time recorded" color="primary" icon="history" />
        <x-stats-card value="{{ number_format($stats['today']) }}" label="Today" subtext="events in last 24 hours" color="info" icon="calendar-event" />
        <x-stats-card value="{{ number_format($stats['this_week']) }}" label="This Week" subtext="last 7 days" color="success" icon="calendar-week" />
        <x-stats-card value="{{ number_format($stats['this_month']) }}" label="This Month" subtext="current month" color="warning" icon="calendar" />
    </div>

    <div class="row g-2">
        <!-- Search, event distribution, and audit trail table - main column -->
        <div class="col-lg-8">
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

            <!-- Search Card -->
            <x-search-card
                placeholder="Search by user, action, or IP address..."
                id="auditTrailSearchInput"
                name="search"
                :value="request('search')"
                :showCategoryFilter="true"
                :showTypeFilter="true"
                :showDateRange="true"
                categoryLabel="Entity"
                typeLabel="Event"
                :categoryOptions="['leave' => 'Leave Requests', 'permission' => 'Permissions', 'travel' => 'Travel', 'acting' => 'Acting Response (Leave)', 'acting_escalation' => 'Acting Escalation', 'incoming_letter' => 'Incoming Letters', 'staff' => 'Staff', 'import' => 'Import', 'auth' => 'Authentication']"
                :typeOptions="['create' => 'Create', 'update' => 'Update', 'approve' => 'Approve', 'reject' => 'Reject', 'assign' => 'Assign', 'dismiss' => 'Dismiss', 'status_update' => 'Status Update', 'bulk_import' => 'Bulk Import', 'login' => 'Login', 'logout' => 'Logout', 'password_change' => 'Password Change', 'view' => 'View']"
                :dateFromValue="request('date_from')"
                :dateToValue="request('date_to')"
            >
                <div id="auditTrailSearchStats" class="text-muted" style="font-size: 0.65rem;">
                    {{ $audits->total() }} events total
                </div>
            </x-search-card>

            <!-- User Filter (page-specific, alongside the shared search card) - only meaningful for viewers who can see everyone's activity -->
            @if($canViewAll)
            <div class="card rounded-sm mb-3">
                <div class="card-body py-2 px-3">
                    <div class="row g-1 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label" style="font-size: 0.65rem;">User</label>
                            <select id="userFilter" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div id="auditTrailResults">
                @include('shared.audit-trail._results', ['audits' => $audits])
            </div>
        </div>

        <!-- Information - narrow column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> About This Dashboard</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>{{ $canViewAll ? 'Shows all activity across the system, for compliance and investigation' : 'Shows only your own activity' }}</li>
                            <li>"Events by Type" and "Entities Monitored" summarize what's currently in view</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-filter me-1 text-primary"></i> Filtering</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>Filter by Entity or Event to narrow results</li>
                            @if($canViewAll)
                            <li>Filter by User to see one person's activity</li>
                            @endif
                            <li>Use "Export CSV" to download the current filtered view</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Records are immutable and kept for audit purposes</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const exportLink = document.querySelector('a[href*="audit-trail/export"]');

    initLiveSearch({
        url: '{{ route('audit-trail.index') }}',
        resultsSelector: '#auditTrailResults',
        statsSelector: '#auditTrailSearchStats',
        searchInputSelector: '#auditTrailSearchInput',
        suggestionsSelector: '#auditTrailSearchInputSuggestions',
        recentSearchesKey: 'auditTrailRecentSearches',
        statLabel: 'events',
        perPage: 50,
        filterSelectors: ['#categoryFilter', '#typeFilter', '#dateFromFilter', '#dateToFilter', '#userFilter'],
        extraParams: () => ({
            entity_type: document.getElementById('categoryFilter')?.value || '',
            event: document.getElementById('typeFilter')?.value || '',
            date_from: document.getElementById('dateFromFilter')?.value || '',
            date_to: document.getElementById('dateToFilter')?.value || '',
            user_id: document.getElementById('userFilter')?.value || '',
        }),
        onUpdate: (data, params) => {
            if (exportLink) {
                exportLink.href = '{{ route('audit-trail.export') }}?' + params.toString();
            }
        },
    });
});
</script>
@endsection