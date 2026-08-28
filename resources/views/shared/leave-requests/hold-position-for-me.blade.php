@extends('layouts.master')

@section('title', 'Acting Requests')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Acting Requests', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="ACTING REQUESTS" description="Leave requests where you are requested to hold position">
        <a href="{{ route('leave-requests.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Apply for Leave
        </a>
    </x-page-header>
    
    <!-- Stats Cards -->
    @php
        $stats = [
            'total' => $holdHistories->total() ?? 0,
            'pending' => $holdHistories->where('status', 'pending')->count(),
            'accepted' => $holdHistories->where('status', 'accepted')->count(),
            'declined' => $holdHistories->where('status', 'declined')->count(),
        ];
    @endphp
    
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Total Requests" subtext="all time" />
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
    
    <!-- Acting Requests Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-user-check text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Acting Requests</span>
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
                        @if($history->status == 'pending')
                        <button type="button" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#actingResponseModal{{ $history->id }}">
                            <i class="ti ti-user-check me-1"></i> Respond
                        </button>
                        @endif
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
            
            <!-- Acting Response Modal -->
            <div class="modal fade" id="actingResponseModal{{ $history->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-sm">
                        <div class="modal-header py-2 px-3 bg-light">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-user-check text-primary me-2" style="font-size: 1rem;"></i>
                                <div>
                                    <h5 class="modal-title" style="font-size: 0.9rem; font-weight: 600;">Hold Position Request</h5>
                                    <p class="text-muted mb-0" style="font-size: 0.65rem;">{{ $history->requester->first_name ?? '' }} {{ $history->requester->last_name ?? '' }} requests you to hold their position</p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('leave-requests.acting.process', $history->leaveRequest->id) }}" method="POST">
                            @csrf
                            <div class="modal-body p-3">
                                <!-- Leave Details -->
                                <div class="bg-light p-2 rounded-sm mb-3">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="text-muted" style="font-size: 0.6rem;">Leave Type</div>
                                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $history->leaveRequest->leaveType->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted" style="font-size: 0.6rem;">Duration</div>
                                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $history->leaveRequest->days }} day(s)</div>
                                        </div>
                                        <div class="col-12">
                                            <div class="text-muted" style="font-size: 0.6rem;">Leave Period</div>
                                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                                {{ \Carbon\Carbon::parse($history->leaveRequest->start_date)->format('d M Y') }} - 
                                                {{ \Carbon\Carbon::parse($history->leaveRequest->end_date)->format('d M Y') }}
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="text-muted" style="font-size: 0.6rem;">Reason</div>
                                            <div class="small" style="font-size: 0.7rem;">{{ $history->leaveRequest->reason }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Comment -->
                                <div class="mb-3">
                                    <label class="form-label" style="font-size: 0.65rem;">Comment (Optional)</label>
                                    <textarea name="comment" class="form-control form-control-sm rounded-sm" rows="2" style="font-size: 0.7rem;" placeholder="Add any remarks..."></textarea>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="d-flex gap-2">
                                    <input type="hidden" name="action" id="action_value_{{ $history->id }}">
                                    <button type="button" class="btn btn-success btn-sm flex-fill rounded-sm" onclick="submitActingResponse({{ $history->id }}, 'accept')" style="font-size: 0.7rem; padding: 0.4rem;">
                                        <i class="ti ti-check me-1"></i> Accept & Hold Position
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm flex-fill rounded-sm" onclick="submitActingResponse({{ $history->id }}, 'decline')" style="font-size: 0.7rem; padding: 0.4rem;">
                                        <i class="ti ti-x me-1"></i> Decline
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4">
                <i class="ti ti-user-check" style="font-size: 2rem; color: #CBD5E1;"></i>
                <p class="text-muted mt-2" style="font-size: 0.75rem;">No acting requests found.</p>
                <p class="text-muted" style="font-size: 0.65rem;">When someone requests you to hold their position, it will appear here.</p>
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
function submitActingResponse(id, action) {
    document.getElementById('action_value_' + id).value = action;
    document.querySelector('#actingResponseModal' + id + ' form').submit();
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