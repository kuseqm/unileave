@extends('layouts.master')

@section('title', 'Access Requests')

@section('content')
<div class="container-fluid px-2">
    <!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['name' => 'Messages', 'url' => route('messages.inbox')],
    ['name' => 'Access Requests', 'url' => '#']
    ]" />
    <x-page-header title="ACCESS REQUESTS" description="Review and approve/deny HR requests to view encrypted message content">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>

    <div class="row g-3">
        <!-- LEFT COLUMN: Access Requests List (70%) -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-2 px-3 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-clock me-1"></i>
                        <span class="fw-semibold">Pending Access Requests</span>
                        <span class="badge bg-warning ms-2">{{ $requests->total() }} pending</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($requests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Request Date</th>
                                        <th>Requested By</th>
                                        <th>Message Subject</th>
                                        <th>From</th>
                                        <th>Reason</th>
                                        <th width="200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $req)
                                    <tr>
                                        <td class="small">{{ $req->created_at->format('d M Y, H:i') }}</td>
                                        <td class="small">
                                            {{ $req->requestedBy->first_name }} {{ $req->requestedBy->last_name }}
                                            <div class="text-muted" style="font-size: 0.6rem;">{{ $req->requestedBy->email }}</div>
                                        </td>
                                        <td class="small">{{ Str::limit($req->message->subject, 50) }}</td>
                                        <td class="small">{{ $req->message->sender->first_name ?? 'System' }}</td>
                                        <td class="small">{{ Str::limit($req->reason, 60) }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $req->id }}">
                                                    <i class="ti ti-check me-1"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">
                                                    <i class="ti ti-x me-1"></i> Reject
                                                </button>
                                                <a href="{{ route('messages.metadata', $req->message_id) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="ti ti-history"></i> Metadata
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 bg-light">
                            {{ $requests->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ti ti-check-circle" style="font-size: 48px; color: #10b981;"></i>
                            <p class="mt-3 text-muted">No pending access requests</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Explanation Panel (30%) -->
        <div class="col-lg-4">
            <div class="card rounded-sm border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header py-2 px-3 bg-light">
                    <i class="ti ti-info-circle me-1"></i>
                    <span class="fw-semibold">About Access Requests</span>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <i class="ti ti-key text-primary me-1"></i>
                        <strong class="small">What is this page?</strong>
                        <p class="text-muted small mt-1 mb-0">HR officers request access to encrypted message content for disciplinary or legal proceedings. You review and approve/deny these requests.</p>
                    </div>
                    
                    <div class="mb-3">
                        <i class="ti ti-check-circle text-success me-1"></i>
                        <strong class="small">Approving a Request</strong>
                        <p class="text-muted small mt-1 mb-0">When you approve, a one-time code is generated. Share this code with the HR officer. The code expires in 24 hours and can only be used once.</p>
                    </div>
                    
                    <div class="mb-3">
                        <i class="ti ti-x-circle text-danger me-1"></i>
                        <strong class="small">Rejecting a Request</strong>
                        <p class="text-muted small mt-1 mb-0">Provide a reason for rejection. This will be recorded in the audit trail.</p>
                    </div>
                    
                    <div class="mb-3">
                        <i class="ti ti-shield text-info me-1"></i>
                        <strong class="small">Audit Trail</strong>
                        <p class="text-muted small mt-1 mb-0">All approvals, rejections, and code usage are logged for compliance and non-repudiation purposes.</p>
                    </div>
                    
                    <div class="alert alert-warning small py-2 px-2 mb-0">
                        <i class="ti ti-alert-triangle me-1"></i>
                        <strong>Important:</strong> Only approve requests with valid disciplinary or legal justification. One-time codes are single-use only.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modals -->
@foreach($requests as $req)
<div class="modal fade" id="approveModal{{ $req->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title">Approve Access Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('messages.approve-access', $req->id) }}" method="POST">
                @csrf
                <div class="modal-body py-2 px-2">
                    <p class="mb-2">Approve access for <strong>{{ $req->requestedBy->first_name }} {{ $req->requestedBy->last_name }}</strong> to view:</p>
                    <div class="bg-light p-2 rounded mb-2" style="font-size: 0.75rem;">
                        <strong>Subject:</strong> {{ $req->message->subject }}<br>
                        <strong>From:</strong> {{ $req->message->sender->first_name ?? 'System' }}<br>
                        <strong>Reason:</strong> {{ $req->reason }}
                    </div>
                    <div class="alert alert-info" style="font-size: 0.7rem;">
                        <i class="ti ti-info-circle me-1"></i>
                        A one-time code will be generated and shown to you. Share this code with the HR officer. Valid for 24 hours, single use.
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">Generate Code & Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title">Reject Access Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('messages.reject-access', $req->id) }}" method="POST">
                @csrf
                <div class="modal-body py-2 px-2">
                    <div class="mb-2">
                        <label class="form-label">Reason for Rejection:</label>
                        <textarea name="rejection_reason" rows="3" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<style>
.sticky-top {
    position: sticky;
    top: 20px;
    z-index: 1;
}
</style>
@endsection