@extends('layouts.master')

@section('title', 'My Request Status')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'My Request Status', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="MY REQUEST STATUS" description="Track the progress of your leave requests">
        <a href="{{ route('leave-requests.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> New Request
        </a>
    </x-page-header>
    
    <!-- Stats Cards with Clickable Links (using built-in link parameter) -->
    <div class="row g-2 mb-3">
        <x-stats-card 
            value="{{ $stats['total'] }}" 
            label="Total Requests" 
            subtext="all time"
            icon="clipboard-list"
            link="{{ route('leave-requests.status', ['filter' => 'all']) }}" />
        
        <x-stats-card 
            value="{{ $stats['pending'] }}" 
            label="Pending" 
            subtext="awaiting approval" 
            color="warning"
            icon="clock"
            link="{{ route('leave-requests.status', ['filter' => 'pending']) }}" />
        
        <x-stats-card 
            value="{{ $stats['approved'] }}" 
            label="Approved" 
            subtext="granted" 
            color="success"
            icon="check"
            link="{{ route('leave-requests.status', ['filter' => 'approved']) }}" />
        
        <x-stats-card 
            value="{{ $stats['rejected'] }}" 
            label="Rejected" 
            subtext="denied" 
            color="danger"
            icon="x"
            link="{{ route('leave-requests.status', ['filter' => 'rejected']) }}" />
        
        <x-stats-card 
            value="{{ $stats['cancelled'] }}" 
            label="Cancelled" 
            subtext="cancelled by you" 
            color="secondary"
            icon="trash"
            link="{{ route('leave-requests.status', ['filter' => 'cancelled']) }}" />
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
    
    <!-- Requests List -->
    <div class="card rounded-sm">
        <div class="card-header py-2 px-3 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-list text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.75rem; font-weight: 600;">My Leave Requests</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    Showing {{ $leaveRequests->firstItem() ?? 0 }} to {{ $leaveRequests->lastItem() ?? 0 }} of {{ $leaveRequests->total() ?? 0 }}
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @forelse($leaveRequests as $request)
            <div class="request-item p-3 border-bottom">
                <!-- Request Header -->
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fw-semibold" style="font-size: 0.85rem;">
                                {{ $request->leaveType->name ?? 'Leave Request' }}
                            </span>
                            <span class="badge {{ 
                                $request->status == 'pending' ? 'bg-warning-subtle text-warning' : 
                                ($request->status == 'approved' ? 'bg-success-subtle text-success' : 
                                ($request->status == 'rejected' ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary')) 
                            }} rounded-sm px-2 py-1" style="font-size: 0.6rem;">
                                {{ ucfirst($request->status) }}
                            </span>
                            @if($request->current_approval_level)
                            <span class="badge bg-info-subtle text-info rounded-sm px-2 py-1" style="font-size: 0.6rem;">
                                @php
                                    $levelNames = [1 => 'HOD', 2 => 'Dean', 3 => 'HR', 4 => 'DVC', 5 => 'HR Logistics'];
                                @endphp
                                {{ $levelNames[$request->current_approval_level] ?? 'Processing' }}
                            </span>
                            @endif
                        </div>
                        <div class="text-muted" style="font-size: 0.65rem; margin-top: 4px;">
                            <i class="ti ti-calendar me-1"></i>
                            {{ \Carbon\Carbon::parse($request->start_date)->format('M d, Y') }} - 
                            {{ \Carbon\Carbon::parse($request->end_date)->format('M d, Y') }}
                            <span class="mx-1">•</span>
                            <i class="ti ti-clock me-1"></i>{{ $request->days }} days
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('leave-requests.show', $request->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-eye me-1"></i> Details
                        </a>
                        @if($request->status == 'pending' && !$request->current_approval_level)
                        <button onclick="cancelRequest({{ $request->id }})" class="btn btn-outline-danger btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-x me-1"></i> Cancel
                        </button>
                        @endif
                    </div>
                </div>
                
                <!-- Approval Tracker (using FULL tracker with details) -->
                <div class="mt-2">
                    <x-approval-tracker-full :leaveRequest="$request" />
                </div>
                
                <!-- Brief Timeline Summary -->
                @if($request->approvalLogs && $request->approvalLogs->isNotEmpty())
                <div class="mt-2 pt-2 border-top" style="border-color: #E8E2D5;">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <span class="text-muted" style="font-size: 0.55rem;">Latest activity:</span>
                        @foreach($request->approvalLogs->take(2) as $log)
                        <div class="d-flex align-items-center gap-1">
                            <i class="ti ti-{{ $log->action == 'approved' ? 'check-circle' : ($log->action == 'rejected' ? 'x-circle' : 'clock') }}" 
                               style="font-size: 0.6rem; color: {{ $log->action == 'approved' ? '#10B981' : ($log->action == 'rejected' ? '#EF4444' : '#F59E0B') }};"></i>
                            <span style="font-size: 0.6rem;">
                                {{ ucfirst($log->action) }} @ {{ $log->created_at->format('M d, H:i') }}
                                @if($log->approver)
                                by {{ $log->approver->first_name ?? '' }}
                                @endif
                            </span>
                        </div>
                        @endforeach
                        @if($request->approvalLogs->count() > 2)
                        <span class="text-muted" style="font-size: 0.55rem;">+{{ $request->approvalLogs->count() - 2 }} more</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-4">
                <i class="ti ti-calendar-off" style="font-size: 2rem; color: #CBD5E1;"></i>
                <p class="text-muted mt-2" style="font-size: 0.75rem;">You haven't submitted any leave requests yet.</p>
                <a href="{{ route('leave-requests.create') }}" class="btn btn-primary btn-sm rounded-sm mt-2" style="font-size: 0.7rem;">
                    <i class="ti ti-plus me-1"></i> Submit Your First Request
                </a>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($leaveRequests->hasPages())
        <div class="card-footer py-2 px-3 bg-light">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 0.65rem;">Show:</span>
                    <select id="perPageSelect" class="custom-select-sm" style="font-size: 0.65rem; padding: 0.2rem 1.2rem 0.2rem 0.4rem; border: 1px solid #E8E2D5; border-radius: 4px;">
                        <option value="10" {{ $leaveRequests->perPage() == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $leaveRequests->perPage() == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $leaveRequests->perPage() == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $leaveRequests->perPage() == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                
                <div>
                    {{ $leaveRequests->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
                
                <div class="text-muted" style="font-size: 0.6rem;">
                    Page {{ $leaveRequests->currentPage() }} of {{ $leaveRequests->lastPage() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.request-item {
    transition: background 0.2s ease;
}
.request-item:hover {
    background: #F8FAFC;
}
.custom-select-sm {
    font-size: 0.65rem;
    padding: 0.2rem 1.2rem 0.2rem 0.4rem;
    border: 1px solid #E8E2D5;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}
.custom-select-sm:focus {
    outline: none;
    border-color: #3B82F6;
}
</style>

<script>
// Cancel Request Function
function cancelRequest(requestId) {
    if (confirm('Are you sure you want to cancel this leave request? This action cannot be undone.')) {
        fetch(`/leave-requests/${requestId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to cancel request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}

// Per Page Change Handler
const perPageSelect = document.getElementById('perPageSelect');
if (perPageSelect) {
    perPageSelect.addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', this.value);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });
}
</script>
@endsection