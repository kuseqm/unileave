@extends('layouts.master')

@section('title', 'Outgoing Letter Details')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Outgoing Letters', 'url' => route('registry.outgoing.index')],
        ['name' => 'Letter Details', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="OUTGOING LETTER DETAILS" :description="'Reference: ' . $letter->ref_number">
        <div class="d-flex gap-1">
            <a href="{{ route('registry.outgoing.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-arrow-left me-1"></i> Back
            </a>
        </div>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Content Column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-text text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Letter Content</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1 mb-2">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Reference Number</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $letter->ref_number }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Created</div>
                            <div style="font-size: 0.75rem;">{{ $letter->created_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Recipient</div>
                        <div style="font-size: 0.75rem;">{{ $letter->recipient }}</div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Subject</div>
                        <div style="font-size: 0.75rem;">{{ $letter->subject }}</div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="text-muted" style="font-size: 0.6rem;">Content</div>
                        <div class="p-2 bg-light rounded-sm" style="font-size: 0.75rem; white-space: pre-wrap; font-family: monospace;">
                            {!! nl2br(e($letter->content)) !!}
                        </div>
                    </div>
                    
                    @if($letter->incomingLetter)
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">In Response To</div>
                        <a href="{{ route('registry.incoming.show', $letter->incomingLetter->id) }}" style="font-size: 0.75rem;">
                            {{ $letter->incomingLetter->ref_number }} - {{ \Illuminate\Support\Str::limit($letter->incomingLetter->subject, 50) }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Status & Actions Column -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Status</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Current Status</div>
                        <div class="mt-1">
                            @if($letter->status == 'draft')
                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Draft</span>
                            @elseif($letter->status == 'pending_approval')
                                <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Pending Approval</span>
                            @elseif($letter->status == 'approved')
                                <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Approved</span>
                            @elseif($letter->status == 'dispatched')
                                <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Dispatched</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($letter->status == 'draft')
                        <form method="POST" action="{{ route('registry.outgoing.submit', $letter->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm w-100 rounded-sm mb-2" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                                <i class="ti ti-send me-1"></i> Submit for Approval
                            </button>
                        </form>
                    @endif
                    
                    @if($letter->status == 'pending_approval')
                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('registry.outgoing.approve', $letter->id) }}" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm w-100 rounded-sm" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                                    <i class="ti ti-check me-1"></i> Approve
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#rejectModal" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                                <i class="ti ti-x me-1"></i> Reject
                            </button>
                        </div>
                    @endif
                    
                    @if($letter->status == 'approved')
                        <form method="POST" action="{{ route('registry.outgoing.dispatch', $letter->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-100 rounded-sm" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                                <i class="ti ti-mail-forward me-1"></i> Dispatch
                            </button>
                        </form>
                    @endif
                    
                    @if($letter->requires_print && $letter->status == 'approved')
                        <x-alert-warning>
    This letter requires physical printing.
</x-alert-warning>
                    @endif
                </div>
            </div>
            
            <!-- Details Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-details text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Additional Info</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Drafted By</div>
                        <div style="font-size: 0.75rem;">{{ $letter->draftedBy->first_name ?? '' }} {{ $letter->draftedBy->last_name ?? $letter->draftedBy->name ?? 'Unknown' }}</div>
                    </div>
                    
                    @if($letter->approved_by)
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Approved By</div>
                        <div style="font-size: 0.75rem;">{{ $letter->approvedBy->first_name ?? '' }} {{ $letter->approvedBy->last_name ?? $letter->approvedBy->name ?? 'Unknown' }}</div>
                    </div>
                    @endif
                    
                    @if($letter->dispatched_at)
                    <div>
                        <div class="text-muted" style="font-size: 0.6rem;">Dispatched At</div>
                        <div style="font-size: 0.75rem;">{{ $letter->dispatched_at->format('d M Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <form method="POST" action="{{ route('registry.outgoing.reject', $letter->id) }}">
                @csrf
                <div class="modal-header py-1 px-2 bg-light">
                    <h5 class="modal-title" style="font-size: 0.8rem;"><i class="ti ti-alert-triangle text-danger me-1"></i> Reject Letter</h5>
                    <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-2 px-2">
                    <div class="mb-2">
                        <label class="form-label text-muted" style="font-size: 0.65rem;">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control form-control-sm" rows="3" style="font-size: 0.7rem;" required></textarea>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.65rem;">Confirm Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection