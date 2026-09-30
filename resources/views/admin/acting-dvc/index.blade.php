@extends('layouts.master')

@section('title', 'Acting DVC Assignments')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Admin', 'url' => '#'],
        ['name' => 'Acting DVC', 'url' => '#']
    ]" />
    
    <x-page-header title="ACTING DVC ASSIGNMENTS" description="Assign staff to act as DVC when the substantive DVC is on leave">
        <a href="{{ route('admin.acting-dvc.create') }}" class="btn btn-primary btn-sm rounded-sm">
            <i class="ti ti-plus me-1"></i> New Assignment
        </a>
    </x-page-header>
    
    <x-alert-success />
    
    <x-alert-danger />
    
    <!-- Active Assignments -->
    <div class="card rounded-sm mb-3">
        <div class="card-header bg-success-subtle py-1 px-2">
            <i class="ti ti-user-check me-1"></i>
            <span style="font-weight: 600;">Active Assignments</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Staff</th>
                            <th class="py-1">Role</th>
                            <th class="py-1">Start Date</th>
                            <th class="py-1">End Date</th>
                            <th class="py-1">Status</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeAssignments as $assignment)
                        <tr>
                            <td class="ps-2 py-1">
                                {{ $assignment->staff->first_name }} {{ $assignment->staff->last_name }}
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $assignment->staff->staff_id ?? '' }}</div>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-primary-subtle text-primary rounded-sm">
                                    {{ strtoupper($assignment->role) }}
                                </span>
                            </td>
                            <td class="py-1">{{ $assignment->start_date->format('d M Y') }}</td>
                            <td class="py-1">{{ $assignment->end_date->format('d M Y') }}</td>
                            <td class="py-1">
                                <span class="badge bg-success-subtle text-success rounded-sm">Active</span>
                            </td>
                            <td class="pe-2 py-1">
                                <form action="{{ route('admin.acting-dvc.destroy', $assignment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-sm" onclick="confirmSubmit(this, 'End this assignment?', { confirmText: 'End Assignment' })">
                                        <i class="ti ti-trash"></i> End
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-2 text-muted">No active assignments</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Pending Assignments -->
    @if($pendingAssignments->count() > 0)
    <div class="card rounded-sm mb-3">
        <div class="card-header bg-warning-subtle py-1 px-2">
            <i class="ti ti-clock me-1"></i>
            <span style="font-weight: 600;">Pending Assignments</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Staff</th>
                            <th class="py-1">Role</th>
                            <th class="py-1">Start Date</th>
                            <th class="py-1">End Date</th>
                            <th class="py-1">Reason</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingAssignments as $assignment)
                        <tr>
                            <td class="ps-2 py-1">
                                {{ $assignment->staff->first_name }} {{ $assignment->staff->last_name }}
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $assignment->staff->staff_id ?? '' }}</div>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-info-subtle text-info rounded-sm">{{ strtoupper($assignment->role) }}</span>
                            </td>
                            <td class="py-1">{{ $assignment->start_date->format('d M Y') }}</td>
                            <td class="py-1">{{ $assignment->end_date->format('d M Y') }}</td>
                            <td class="py-1">{{ \Str::limit($assignment->reason, 50) }}</td>
                            <td class="pe-2 py-1">
                                <a href="{{ route('admin.acting-dvc.show', $assignment->id) }}" class="btn btn-sm btn-outline-info rounded-sm">
                                    <i class="ti ti-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    
    <!-- History -->
    <x-search-card
        placeholder="Search by staff name..."
        id="actingDvcSearchInput"
        name="search"
        :value="request('search')"
        :showStatusFilter="true"
        statusLabel="Role"
        :statusOptions="['dvca' => 'DVCA', 'dvc-pfa' => 'DVC-PFA']"
    >
        <div id="actingDvcSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $history->total() }} history records total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- History - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header bg-light py-1 px-2">
                    <i class="ti ti-history me-1"></i>
                    <span style="font-weight: 600;">History</span>
                </div>
                <div id="actingDvcResults">
                    @include('admin.acting-dvc._history-results')
                </div>
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-user-check me-1 text-primary"></i> About Acting Assignments</div>
                        <p class="mb-0" style="font-size: 0.65rem; color: #555;">An Acting DVC assignment temporarily grants a staff member DVCA or DVC-PFA authority while the substantive holder is on leave. New assignments need review before becoming active.</p>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> Understanding Status</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><span class="badge bg-success-subtle text-success rounded-sm">Active</span> - currently in effect</li>
                            <li>Pending - awaiting review before it can start</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Ending an active assignment restores authority to the substantive holder</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Filter history by role to isolate DVCA or DVC-PFA records</small>
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
    initLiveSearch({
        url: '{{ route('admin.acting-dvc.index') }}',
        resultsSelector: '#actingDvcResults',
        statsSelector: '#actingDvcSearchStats',
        searchInputSelector: '#actingDvcSearchInput',
        suggestionsSelector: '#actingDvcSearchInputSuggestions',
        recentSearchesKey: 'actingDvcRecentSearches',
        statLabel: 'history records',
        perPage: 20,
        filterSelectors: ['#statusFilter'],
        extraParams: () => ({
            role: document.getElementById('statusFilter')?.value || '',
        }),
    });
});
</script>
@endsection