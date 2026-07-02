@extends('layouts.master')

@section('title', 'Pending Permission Approvals')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Pending Approvals', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="PENDING PERMISSION APPROVALS" description="Permission and travel requests awaiting your approval">
    </x-page-header>
    
    <!-- Stats Cards -->
    @php
        $totalPending = ($pendingRequests->total() ?? 0) + ($travelPending->count() ?? 0);
        $permissionPending = $pendingRequests->total() ?? 0;
        $travelPendingCount = $travelPending->count() ?? 0;
    @endphp
    
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $totalPending }}" label="Total Pending" subtext="awaiting your decision" color="warning" />
        <x-stats-card value="{{ $permissionPending }}" label="Short Permissions" subtext="awaiting approval" color="info" />
        <x-stats-card value="{{ $travelPendingCount }}" label="Travel Permissions" subtext="awaiting approval" color="primary" />
    </div>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <!-- Short Permissions Pending -->
    <div class="card rounded-sm mb-2">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-clock text-warning me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Pending Short Permissions</span>
                @if($permissionPending > 0)
                <span class="badge bg-warning ms-2" style="font-size: 0.6rem;">{{ $permissionPending }}</span>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if($pendingRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Staff</th>
                            <th class="py-1">Type</th>
                            <th class="py-1">Date & Time</th>
                            <th class="py-1">Hours</th>
                            <th class="py-1">Reason</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRequests as $request)
                        <tr>
                            <td class="ps-2 py-1">
                                <span class="fw-semibold">{{ $request->staff->first_name }} {{ $request->staff->last_name }}</span>
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $request->staff->staff_id }}</div>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">
                                    {{ str_replace('_', ' ', ucfirst($request->permission_type)) }}
                                </span>
                            </td>
                            <td class="py-1">
                                {{ \Carbon\Carbon::parse($request->start_datetime)->format('d M Y H:i') }}<br>
                                <small class="text-muted">to {{ \Carbon\Carbon::parse($request->end_datetime)->format('d M Y H:i') }}</small>
                            </td>
                            <td class="py-1">{{ $request->hours }} h</td>
                            <td class="py-1">{{ Str::limit($request->reason, 50) }}</td>
                            <td class="pe-2 py-1">
                                <button class="btn btn-success btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#approvePermModal{{ $request->id }}" style="padding: 0.2rem 0.4rem; font-size: 0.6rem;">
                                    <i class="ti ti-check"></i> Approve
                                </button>
                                <button class="btn btn-danger btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#rejectPermModal{{ $request->id }}" style="padding: 0.2rem 0.4rem; font-size: 0.6rem;">
                                    <i class="ti ti-x"></i> Reject
                                </button>
                            </td>
                        </tr>
                        
                        <!-- Approve Modal -->
                        <div class="modal fade" id="approvePermModal{{ $request->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-sm">
                                    <form action="{{ route('permissions.approve', $request->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header py-1 px-2">
                                            <h5 class="modal-title" style="font-size: 0.8rem;">Approve Permission Request</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body py-2 px-2">
                                            <p style="font-size: 0.75rem;">Approve permission request from <strong>{{ $request->staff->first_name }} {{ $request->staff->last_name }}</strong>?</p>
                                            <p class="text-muted" style="font-size: 0.65rem;">
                                                Type: {{ str_replace('_', ' ', ucfirst($request->permission_type)) }}<br>
                                                Duration: {{ $request->hours }} hours
                                            </p>
                                        </div>
                                        <div class="modal-footer py-1 px-2">
                                            <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                                            <button type="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.65rem;">Approve</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectPermModal{{ $request->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-sm">
                                    <form action="{{ route('permissions.reject', $request->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header py-1 px-2">
                                            <h5 class="modal-title" style="font-size: 0.8rem;">Reject Permission Request</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body py-2 px-2">
                                            <p style="font-size: 0.75rem;">Reject permission request from <strong>{{ $request->staff->first_name }} {{ $request->staff->last_name }}</strong>?</p>
                                            <div class="mb-2">
                                                <label class="form-label" style="font-size: 0.65rem;">Reason <span class="text-danger">*</span></label>
                                                <textarea name="rejection_reason" class="form-control form-control-sm rounded-sm" rows="2" style="font-size: 0.7rem;" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer py-1 px-2">
                                            <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                                            <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.65rem;">Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-3">
                <i class="ti ti-clock" style="font-size: 1.5rem; color: #CBD5E1;"></i>
                <p class="text-muted mt-1" style="font-size: 0.7rem;">No pending short permission requests</p>
            </div>
            @endif
        </div>
        @if($pendingRequests->hasPages())
        <div class="card-footer py-1 px-2">
            {{ $pendingRequests->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
    
    <!-- Travel Permissions Pending -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-plane text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Pending Travel Approvals</span>
                @if($travelPendingCount > 0)
                <span class="badge bg-warning ms-2" style="font-size: 0.6rem;">{{ $travelPendingCount }}</span>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if($travelPending->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Staff</th>
                            <th class="py-1">Destination</th>
                            <th class="py-1">Travel Period</th>
                            <th class="py-1">Days</th>
                            <th class="py-1">Reason</th>
                            <th class="py-1">Cost Coverage</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($travelPending as $request)
                        <tr>
                            <td class="ps-2 py-1">
                                <span class="fw-semibold">{{ $request->staff->first_name }} {{ $request->staff->last_name }}</span>
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $request->staff->staff_id }}</div>
                            </td>
                            <td class="py-1">{{ $request->destination }}</td>
                            <td class="py-1">
                                {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }}<br>
                                <small class="text-muted">to {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</small>
                            </td>
                            <td class="py-1">{{ $request->days }}</td>
                            <td class="py-1">{{ ucfirst($request->travel_reason) }}</td>
                            <td class="py-1">
                                @php
                                    $coverageLabels = [
                                        'organizers_full' => 'Organizers Full',
                                        'organizers_partial' => 'Organizers Partial',
                                        'university' => 'University',
                                        'private' => 'Private'
                                    ];
                                @endphp
                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.55rem;">
                                    {{ $coverageLabels[$request->cost_coverage] ?? $request->cost_coverage }}
                                </span>
                            </td>
                            <td class="pe-2 py-1">
                                <a href="{{ route('travel-permissions.pending') }}" class="btn btn-primary btn-sm rounded-sm" style="padding: 0.2rem 0.4rem; font-size: 0.6rem;">
                                    <i class="ti ti-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-3">
                <i class="ti ti-plane" style="font-size: 1.5rem; color: #CBD5E1;"></i>
                <p class="text-muted mt-1" style="font-size: 0.7rem;">No pending travel permission requests</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.modal-content {
    border: none;
}
</style>
@endsection