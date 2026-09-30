@extends('layouts.master')

@section('title', 'Audit Trail Details')

@section('content')
<div class="container-fluid px-2">
    <x-page-header title="AUDIT TRAIL DETAILS" description="Complete event information for compliance">
        <a href="{{ route('audit-trail.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back to Audit Trail
        </a>
    </x-page-header>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-2 px-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="ti ti-info-circle me-1"></i>
                            <span class="fw-semibold">Event Information</span>
                        </div>
                        {!! $audit->event_badge !!}
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <span class="text-muted small">Event ID:</span>
                                <div class="small">#{{ $audit->id }}</div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small">Date/Time:</span>
                                <div class="small">{{ $audit->created_at->format('d M Y, H:i:s') }}</div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small">User:</span>
                                <div class="small">{{ $audit->user->first_name ?? 'System' }} {{ $audit->user->last_name ?? '' }}</div>
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $audit->user->email ?? '' }}</div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small">User Role:</span>
                                <div class="small">{{ $audit->user_role ?? 'Unknown' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <span class="text-muted small">Entity Type:</span>
                                <div class="small">{{ $audit->entity_type }}</div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small">Entity ID:</span>
                                <div class="small">{{ $audit->auditable_id }}</div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small">Event:</span>
                                <div class="small">{{ ucfirst($audit->event) }}</div>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small">Action:</span>
                                <div class="small">{{ $audit->action }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <span class="text-muted small">IP Address:</span>
                                <div class="small"><code>{{ $audit->ip_address ?? 'N/A' }}</code></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <span class="text-muted small">User Agent:</span>
                                <div class="small text-break">{{ $audit->user_agent ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($audit->metadata)
                        <hr>
                        <div class="mb-2">
                            <span class="text-muted small">Metadata:</span>
                            <pre class="small bg-light p-2 rounded mt-1" style="font-size: 0.7rem;">{{ json_encode($audit->metadata, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                    
                    @if($audit->old_values)
                        <hr>
                        <div class="mb-2">
                            <span class="text-muted small">Old Values:</span>
                            <pre class="small bg-light p-2 rounded mt-1" style="font-size: 0.7rem;">{{ json_encode($audit->old_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                    
                    @if($audit->new_values)
                        <hr>
                        <div class="mb-2">
                            <span class="text-muted small">New Values:</span>
                            <pre class="small bg-light p-2 rounded mt-1" style="font-size: 0.7rem;">{{ json_encode($audit->new_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card rounded-sm border-0 shadow-sm">
                <div class="card-header py-2 px-3 bg-light">
                    <i class="ti ti-info-circle me-1"></i>
                    <span class="fw-semibold">About Audit Trail</span>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <i class="ti ti-shield text-primary me-1"></i>
                        <strong class="small">Non-Repudiation</strong>
                        <p class="text-muted small mt-1 mb-0">This audit trail provides legally admissible evidence of all system activities.</p>
                    </div>
                    
                    <div class="mb-3">
                        <i class="ti ti-clock text-info me-1"></i>
                        <strong class="small">Retention Period</strong>
                        <p class="text-muted small mt-1 mb-0">Audit records are retained for 7 years as required by law.</p>
                    </div>
                    
                    <x-alert-warning>
    <strong>Legal Notice:</strong> Tampering with audit logs is prohibited and constitutes a criminal offense.
</x-alert-warning>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection