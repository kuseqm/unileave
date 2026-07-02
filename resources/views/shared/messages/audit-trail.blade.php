@extends('layouts.master')

@section('title', 'Message Audit Trail')

@section('content')
<div class="container-fluid px-2">
    <x-page-header title="MESSAGE AUDIT TRAIL" description="Complete history of this message">
        <a href="{{ route('messages.admin-list') }}" class="btn btn-outline-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back to List
        </a>
    </x-page-header>

    <div class="row">
        <div class="col-lg-4">
            <!-- Message Summary -->
            <div class="card rounded-sm mb-3">
                <div class="card-header py-2 px-3 bg-light">
                    <i class="ti ti-info-circle me-1"></i> Message Summary
                </div>
                <div class="card-body p-3">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted small" width="40%">ID:</td>
                            <td class="small">#{{ $message->id }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Number:</td>
                            <td class="small">{{ $message->message_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Subject:</td>
                            <td class="small">{{ $message->subject }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">From:</td>
                            <td class="small">{{ $message->sender->first_name ?? 'System' }} {{ $message->sender->last_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">To:</td>
                            <td class="small">{{ $message->recipient_names ?? $message->receiver->first_name ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Created:</td>
                            <td class="small">{{ $message->created_at->format('d M Y, H:i:s') }}</td>
                        </tr>
                        @if($message->deleted_at)
                        <tr>
                            <td class="text-muted small">Deleted:</td>
                            <td class="small text-warning">{{ $message->deleted_at->format('d M Y, H:i:s') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Access Requests -->
            <div class="card rounded-sm">
                <div class="card-header py-2 px-3 bg-light">
                    <i class="ti ti-key me-1"></i> Access Requests
                </div>
                <div class="card-body p-3">
                    @if($message->accessRequests->count() > 0)
                        @foreach($message->accessRequests as $request)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="small fw-semibold">{{ $request->requestedBy->first_name ?? '' }} {{ $request->requestedBy->last_name ?? '' }}</span>
                                    <span class="badge bg-{{ $request->status == 'approved' ? 'success' : ($request->status == 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($request->status) }}</span>
                                </div>
                                <div class="text-muted small">{{ $request->created_at->format('d M Y, H:i') }}</div>
                                <div class="small mt-1">{{ Str::limit($request->reason, 100) }}</div>
                                @if($request->approved_by)
                                    <div class="text-muted small mt-1">Approved by: {{ $request->approvedBy->first_name ?? '' }}</div>
                                @endif
                                @if($request->viewed_at)
                                    <div class="text-muted small">Viewed at: {{ $request->viewed_at->format('d M Y, H:i:s') }}</div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted small mb-0">No access requests for this message.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Audit Log -->
            <div class="card rounded-sm">
                <div class="card-header py-2 px-3 bg-light">
                    <i class="ti ti-history me-1"></i> Activity Timeline
                </div>
                <div class="card-body p-0">
                    @if($message->auditLogs->count() > 0)
                        <div class="timeline p-3">
                            @foreach($message->auditLogs as $log)
                                <div class="timeline-item mb-3">
                                    <div class="d-flex gap-3">
                                        <div class="timeline-icon">
                                            @if($log->action == 'accessed_via_code')
                                                <i class="ti ti-key text-warning"></i>
                                            @elseif($log->action == 'soft_deleted')
                                                <i class="ti ti-trash text-danger"></i>
                                            @elseif($log->action == 'viewed')
                                                <i class="ti ti-eye text-info"></i>
                                            @else
                                                <i class="ti ti-check-circle text-success"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-semibold small">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                                                <span class="text-muted small">{{ $log->created_at->format('d M Y, H:i:s') }}</span>
                                            </div>
                                            <div class="small">By: {{ $log->user->first_name ?? 'System' }} {{ $log->user->last_name ?? '' }}</div>
                                            @if($log->access_method)
                                                <div class="text-muted small">Method: {{ ucfirst($log->access_method) }}</div>
                                            @endif
                                            @if($log->ip_address)
                                                <div class="text-muted small">IP: {{ $log->ip_address }}</div>
                                            @endif
                                            @if($log->one_time_code_used)
                                                <div class="text-muted small">Code used: {{ $log->one_time_code_used }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ti ti-history" style="font-size: 48px; color: #ccc;"></i>
                            <p class="mt-2 text-muted">No audit logs for this message yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-item {
    position: relative;
}
.timeline-icon {
    width: 32px;
    text-align: center;
}
</style>
@endsection