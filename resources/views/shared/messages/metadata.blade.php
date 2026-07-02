@extends('layouts.master')

@section('title', 'Message Metadata & Audit')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Messages', 'url' => route('messages.inbox')],
        ['name' => 'Message Directory', 'url' => route('messages.admin-list')],
        ['name' => 'Message Metadata', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="MESSAGE METADATA & AUDIT TRAIL" description="Non-repudiation record - Content is encrypted">
        <a href="{{ route('messages.admin-list') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Directory
        </a>
    </x-page-header>

    <div class="row g-3">
        <!-- LEFT COLUMN: Message Metadata (70%) -->
        <div class="col-lg-8">
            <!-- Message Metadata Card -->
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Message Metadata</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Message ID:</span>
                                <div class="small" style="font-size: 0.7rem;">#{{ $message->id }}</div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Message Number:</span>
                                <div class="small" style="font-size: 0.7rem;">{{ $message->message_number }}</div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Subject:</span>
                                <div class="small" style="font-size: 0.7rem;">{{ $message->subject }}</div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Priority:</span>
                                <div>{!! $message->priority_badge !!}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">From:</span>
                                <div class="small" style="font-size: 0.7rem;">{{ $message->sender->first_name ?? 'System' }} {{ $message->sender->last_name ?? '' }}</div>
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $message->sender->email ?? '' }}</div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">To:</span>
                                <div class="small" style="font-size: 0.7rem;">{{ $message->recipient_names ?? $message->receiver->first_name ?? 'Unknown' }}</div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Status:</span>
                                <div>{!! $message->status_badge !!}</div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-2">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Created:</span>
                                <div class="small" style="font-size: 0.7rem;">{{ $message->created_at->format('d M Y, H:i:s') }}</div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Delivered:</span>
                                <div class="small" style="font-size: 0.7rem;">{{ $message->delivered_at ? $message->delivered_at->format('d M Y, H:i:s') : 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Read:</span>
                                <div class="small" style="font-size: 0.7rem;">{{ $message->read_at ? $message->read_at->format('d M Y, H:i:s') : 'Unread' }}</div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small" style="font-size: 0.65rem;">Replied:</span>
                                <div class="small" style="font-size: 0.7rem;">{{ $message->replied_at ? $message->replied_at->format('d M Y, H:i:s') : 'No' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($message->deleted_at)
                        <div class="alert alert-warning py-1 px-2 mb-0 mt-2" style="font-size: 0.65rem;">
                            <i class="ti ti-trash me-1"></i> Soft deleted on {{ $message->deleted_at->format('d M Y, H:i:s') }}
                        </div>
                    @endif
                    
                    <!-- Content Access Warning -->
                    <div class="alert alert-warning mt-3 mb-0 py-1 px-2" style="font-size: 0.65rem;">
                        <i class="ti ti-lock me-1"></i>
                        <strong>Content Encrypted</strong> - The actual message content is encrypted and cannot be viewed here.
                    </div>
                </div>
            </div>
            
            <!-- Access Requests History -->
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-key text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Access Request History</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($message->accessRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-1">Requested By</th>
                                        <th class="py-1">Date</th>
                                        <th class="py-1">Reason</th>
                                        <th class="py-1">Status</th>
                                        <th class="py-1">Code Used</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($message->accessRequests as $request)
                                    <tr>
                                        <td class="py-1">{{ $request->requestedBy->first_name ?? '' }} {{ $request->requestedBy->last_name ?? '' }}</td>
                                        <td class="py-1">{{ $request->created_at->format('d M Y') }}</td>
                                        <td class="py-1">{{ Str::limit($request->reason, 40) }}</td>
                                        <td class="py-1">
                                            <span class="badge bg-{{ $request->status == 'approved' ? 'success' : ($request->status == 'pending' ? 'warning' : 'danger') }} rounded-sm" style="font-size: 0.6rem;">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td class="py-1">
                                            @if($request->viewed_at)
                                                <i class="ti ti-check-circle text-success"></i> Used
                                            @elseif($request->status == 'approved')
                                                <i class="ti ti-clock text-warning"></i> Pending
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0" style="font-size: 0.7rem;">No access requests for this message.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Audit Log -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Activity Timeline</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($message->auditLogs->count() > 0)
                        <div class="timeline p-2">
                            @foreach($message->auditLogs as $log)
                                <div class="timeline-item mb-2 pb-2 border-bottom">
                                    <div class="d-flex gap-2">
                                        <div class="timeline-icon">
                                            @if($log->action == 'accessed_via_code')
                                                <i class="ti ti-key text-warning"></i>
                                            @elseif($log->action == 'soft_deleted')
                                                <i class="ti ti-trash text-danger"></i>
                                            @elseif($log->action == 'viewed')
                                                <i class="ti ti-eye text-info"></i>
                                            @elseif($log->action == 'replied')
                                                <i class="ti ti-corner-up-right text-success"></i>
                                            @else
                                                <i class="ti ti-check-circle text-success"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold" style="font-size: 0.7rem;">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</div>
                                            <div class="text-muted" style="font-size: 0.6rem;">{{ $log->user->first_name ?? 'System' }} {{ $log->user->last_name ?? '' }} • {{ $log->created_at->format('d M Y, H:i:s') }}</div>
                                            @if($log->ip_address)
                                                <div class="text-muted" style="font-size: 0.6rem;">IP: {{ $log->ip_address }}</div>
                                            @endif
                                            @if($log->one_time_code_used)
                                                <div class="text-muted" style="font-size: 0.6rem;">One-time code used: <code>{{ $log->one_time_code_used }}</code></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0" style="font-size: 0.7rem;">No activity recorded for this message.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Actions Panel (30%) -->
        <div class="col-lg-4">
            <!-- Actions Card -->
            <div class="card rounded-sm border-0 shadow-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-settings text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Actions</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <!-- Restore Button (only for soft-deleted messages) -->
                    @if($message->trashed())
                        <div class="mb-2">
                            <button onclick="restoreMessage({{ $message->id }})" class="btn btn-outline-success btn-sm w-100 rounded-sm" style="font-size: 0.7rem; padding: 0.3rem 0.5rem;">
                                <i class="ti ti-reload me-1"></i> Restore Message
                            </button>
                            <p class="text-muted mt-1 mb-0" style="font-size: 0.6rem;">Restore this message from soft delete.</p>
                        </div>
                    @endif
                    
                    <!-- Request Access Button (HR only, for active messages) -->
                    @if(auth()->user()->hasRole('hr_manager') && !$message->trashed())
                        <div class="mb-2">
                            <button onclick="requestAccess({{ $message->id }})" class="btn btn-outline-warning btn-sm w-100 rounded-sm" style="font-size: 0.7rem; padding: 0.3rem 0.5rem;">
                                <i class="ti ti-key me-1"></i> Request Access to Content
                            </button>
                            <p class="text-muted mt-1 mb-0" style="font-size: 0.6rem;">Request VC approval to view encrypted message content.</p>
                        </div>
                    @endif
                    
                    <!-- View Content (Only for sender/receiver or VC-approved) -->
                    @if($message->canViewContent(auth()->user()))
                        <div class="mb-2">
                            <a href="{{ route('messages.secure-content', $message->id) }}" class="btn btn-primary btn-sm w-100 rounded-sm" style="font-size: 0.7rem; padding: 0.3rem 0.5rem;">
                                <i class="ti ti-eye me-1"></i> View Message Content
                            </a>
                            <p class="text-muted mt-1 mb-0" style="font-size: 0.6rem;">You have permission to view the actual message content.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- About This Page -->
            <div class="card rounded-sm border-0 shadow-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">About This Page</span>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="mb-2">
                        <i class="ti ti-shield text-primary me-1" style="font-size: 0.7rem;"></i>
                        <strong class="small" style="font-size: 0.7rem;">Non-Repudiation</strong>
                        <p class="text-muted mt-1 mb-0" style="font-size: 0.65rem;">This page provides complete metadata and audit trail for compliance purposes.</p>
                    </div>
                    
                    <div class="mb-2">
                        <i class="ti ti-key text-info me-1" style="font-size: 0.7rem;"></i>
                        <strong class="small" style="font-size: 0.7rem;">Access Request Process</strong>
                        <p class="text-muted mt-1 mb-0" style="font-size: 0.65rem;">HR can request access to message content. VC approves and generates a one-time code valid for 24 hours.</p>
                    </div>
                    
                    <div class="alert alert-info py-1 px-2 mb-0" style="font-size: 0.65rem;">
                        <i class="ti ti-tips me-1"></i>
                        <strong>Note:</strong> All timestamps are in your local timezone. Data is retained for 7 years as required by law.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function restoreMessage(id) {
    if(confirm('Restore this message? It will become active again.')) {
        fetch(`/messages/${id}/restore`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => window.location.href = '{{ route("messages.admin-list") }}');
    }
}

function requestAccess(id) {
    let reason = prompt('Please provide a detailed reason for accessing this message content (min 10 characters):');
    if (reason && reason.length >= 10) {
        fetch(`/messages/${id}/request-access`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        }).then(response => response.json())
          .then(data => alert(data.message || 'Request sent to VC for approval'))
          .catch(() => alert('Request sent to VC for approval'));
    } else if (reason) {
        alert('Reason must be at least 10 characters');
    }
}
</script>

<style>
.timeline-icon {
    width: 28px;
    text-align: center;
}
.timeline-item:last-child {
    border-bottom: none !important;
}
</style>
@endsection