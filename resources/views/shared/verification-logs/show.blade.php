@extends('layouts.master')

@section('title', 'Verification Scan Details')

@section('content')
<div class="container-fluid px-2">

    <!-- Page Header -->
    <x-page-header title="VERIFICATION SCAN DETAILS" description="Full record of a single QR-code scan">
        <a href="{{ route('verification-logs.index', $log->is_archived ? ['view' => 'archived'] : []) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Log
        </a>
    </x-page-header>

    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-scan text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Scan #{{ $log->id }}</span>
                    </div>
                    @if($log->successful)
                        <span class="badge bg-success rounded-sm" style="font-size: 0.65rem;"><i class="ti ti-check"></i> Verified</span>
                    @else
                        <span class="badge bg-danger rounded-sm" style="font-size: 0.65rem;"><i class="ti ti-x"></i> Failed</span>
                    @endif
                </div>
                <div class="card-body p-3">
                    <div class="row g-3" style="font-size: 0.75rem;">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.65rem;">Date / Time</div>
                            <div class="fw-semibold">{{ $log->created_at->format('d M Y, H:i:s') }}</div>
                            <div class="text-muted" style="font-size: 0.6rem;">{{ $log->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.65rem;">Document Type</div>
                            <div class="fw-semibold">
                                <span class="badge bg-info rounded-sm" style="font-size: 0.65rem;">{{ str_replace('_', ' ', ucfirst($log->document_type)) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.65rem;">Document ID</div>
                            <div class="fw-semibold">{{ $log->document_id ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.65rem;">Scanned Code</div>
                            <div class="fw-semibold"><code>{{ $log->code }}</code></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.65rem;">University</div>
                            <div class="fw-semibold">{{ $log->university->name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.65rem;">IP Address</div>
                            <div class="fw-semibold"><code>{{ $log->ip_address ?? 'N/A' }}</code></div>
                        </div>
                        <div class="col-md-12">
                            <div class="text-muted" style="font-size: 0.65rem;">Device / User Agent</div>
                            <div class="fw-semibold" style="word-break: break-word;">{{ $log->user_agent ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-12">
                            <div class="text-muted" style="font-size: 0.65rem;">Status</div>
                            <div class="fw-semibold">
                                @if($log->is_archived)
                                    <span class="badge bg-dark rounded-sm" style="font-size: 0.65rem;"><i class="ti ti-archive"></i> Archived</span>
                                @else
                                    <span class="badge bg-light text-dark border rounded-sm" style="font-size: 0.65rem;">Active</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer py-2 px-3">
                    @if($log->is_archived)
                        <form action="{{ route('verification-logs.unarchive', $log->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-archive-off me-1"></i> Restore to Active
                            </button>
                        </form>
                    @else
                        <form action="{{ route('verification-logs.archive', $log->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-archive me-1"></i> Archive
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <x-alert-light class="mb-0 py-2 px-2 rounded-sm">
                <div class="d-flex align-items-start gap-1">
                    <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                    <div>
                        <strong class="d-block" style="font-size: 0.65rem;">About Archiving</strong>
                        <small class="text-muted" style="font-size: 0.6rem;">Archiving only hides a record from the default list - it is kept permanently and can be restored at any time.</small>
                    </div>
                </div>
            </x-alert-light>
        </div>
    </div>
</div>
@endsection
