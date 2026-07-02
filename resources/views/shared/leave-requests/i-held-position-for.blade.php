@extends('layouts.master')

@section('title', 'My Assignments')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'My Assignments', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="MY ASSIGNMENTS" description="Leave requests where you held position for others">
        <a href="{{ route('leave-requests.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Apply for Leave
        </a>
    </x-page-header>
    
    <!-- Stats Cards -->
    @php
        $stats = [
            'total' => $holdHistories->total(),
            'pending' => $holdHistories->where('status', 'pending')->count(),
            'accepted' => $holdHistories->where('status', 'accepted')->count(),
            'declined' => $holdHistories->where('status', 'declined')->count(),
        ];
    @endphp
    
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Total Assignments" subtext="all time" />
        <x-stats-card value="{{ $stats['pending'] }}" label="Pending" subtext="awaiting response" color="warning" />
        <x-stats-card value="{{ $stats['accepted'] }}" label="Accepted" subtext="you accepted" color="success" />
        <x-stats-card value="{{ $stats['declined'] }}" label="Declined" subtext="you declined" color="danger" />
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
    
    <!-- My Assignments Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-user-check text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">My Assignments</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    Showing {{ $holdHistories->firstItem() ?? 0 }} to {{ $holdHistories->lastItem() ?? 0 }} of {{ $holdHistories->total() ?? 0 }}
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @forelse($holdHistories as $history)
            <div class="request-item p-3 border-bottom">
                <!-- Request Header -->
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-semibold" style="font-size: 0.85rem;">
                                {{ $history->requester->first_name ?? 'Unknown' }} {{ $history->requester->last_name ?? '' }}
                            </span>
                            <span class="badge {{ 
                                $history->status == 'pending' ? 'bg-warning-subtle text-warning' : 
                                ($history->status == 'accepted' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger') 
                            }} rounded-sm px-2 py-1" style="font-size: 0.6rem;">
                                {{ ucfirst($history->status) }}
                            </span>
                        </div>
                        <div class="text-muted" style="font-size: 0.65rem; margin-top: 4px;">
                            <i class="ti ti-calendar me-1"></i>
                            {{ \Carbon\Carbon::parse($history->leaveRequest->start_date)->format('M d, Y') }} - 
                            {{ \Carbon\Carbon::parse($history->leaveRequest->end_date)->format('M d, Y') }}
                            <span class="mx-1">•</span>
                            <i class="ti ti-clock me-1"></i>{{ $history->leaveRequest->days }} days
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('leave-requests.show', $history->leaveRequest->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-eye me-1"></i> Details
                        </a>
                    </div>
                </div>
                
                <!-- Leave Type and Reason -->
                <div class="mt-2">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="text-muted" style="font-size: 0.6rem;">Leave Type</div>
                            <div style="font-size: 0.7rem;">{{ $history->leaveRequest->leaveType->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-8">
                            <div class="text-muted" style="font-size: 0.6rem;">Reason</div>
                            <div style="font-size: 0.7rem;">{{ Str::limit($history->leaveRequest->reason, 100) }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Response Comment if any -->
                @if($history->comment)
                <div class="mt-2 pt-2 border-top" style="border-color: #E8E2D5;">
                    <div class="text-muted" style="font-size: 0.6rem;">Your Comment</div>
                    <div class="small" style="font-size: 0.65rem;">"{{ $history->comment }}"</div>
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-4">
                <i class="ti ti-user-check" style="font-size: 2rem; color: #CBD5E1;"></i>
                <p class="text-muted mt-2" style="font-size: 0.75rem;">No assignments found.</p>
                <p class="text-muted" style="font-size: 0.65rem;">When you accept to hold someone's position, it will appear here.</p>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($holdHistories->hasPages())
        <div class="card-footer py-2 px-3 bg-light">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 0.65rem;">Show:</span>
                    <select id="perPageSelect" class="custom-select-sm" style="font-size: 0.65rem; padding: 0.2rem 1.2rem 0.2rem 0.4rem; border: 1px solid #E8E2D5; border-radius: 4px;">
                        <option value="10" {{ $holdHistories->perPage() == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $holdHistories->perPage() == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $holdHistories->perPage() == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $holdHistories->perPage() == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                
                <div>
                    {{ $holdHistories->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
                
                <div class="text-muted" style="font-size: 0.6rem;">
                    Page {{ $holdHistories->currentPage() }} of {{ $holdHistories->lastPage() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
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
</style>
@endsection