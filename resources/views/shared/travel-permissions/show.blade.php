@extends('layouts.master')

@section('title', 'Travel Request Details')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Applications Overview', 'url' => route('applications-overview.index')],
        ['name' => 'Travel Request Details', 'url' => '#']
    ]" />
    
    <x-page-header title="TRAVEL REQUEST DETAILS" description="View travel permission information">
        @if(auth()->id() === $travelRequest->staff_id && $travelRequest->status === 'pending')
        <button type="button" class="btn btn-outline-danger btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;" onclick="confirmCancelTravel()">
            <i class="ti ti-ban me-1"></i> Cancel Request
        </button>
        <form id="cancel-travel-form" action="{{ route('travel-permissions.cancel', $travelRequest->id) }}" method="POST" class="d-none">
            @csrf
        </form>
        @endif
        <a href="{{ route('applications-overview.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <div class="col-lg-7">
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Travel Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Destination</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $travelRequest->destination }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Travel Period</div>
                            <div style="font-size: 0.75rem;">
                                {{ \Carbon\Carbon::parse($travelRequest->start_date)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($travelRequest->end_date)->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-1 mt-1">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Number of Days</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $travelRequest->days }} day(s)</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Cost Coverage</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                @php
                                    $coverageLabels = [
                                        'organizers_full' => 'Organizers (Full Paid)',
                                        'organizers_partial' => 'Organizers (Partially Paid)',
                                        'university' => 'University',
                                        'private' => 'Private Travel'
                                    ];
                                @endphp
                                {{ $coverageLabels[$travelRequest->cost_coverage] ?? $travelRequest->cost_coverage }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Travel Reason</div>
                        <div class="badge bg-{{ $travelRequest->travel_reason == 'official' ? 'primary' : 'secondary' }}-subtle text-{{ $travelRequest->travel_reason == 'official' ? 'primary' : 'secondary' }} rounded-sm px-2 py-1" style="font-size: 0.6rem;">
                            {{ ucfirst($travelRequest->travel_reason) }}
                        </div>
                    </div>
                    
                    @if($travelRequest->travel_reason == 'official' && $travelRequest->official_benefit)
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Benefit to University</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;">{{ $travelRequest->official_benefit }}</div>
                    </div>
                    @endif
                    
                    @if($travelRequest->travel_reason == 'personal' && $travelRequest->personal_description)
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Description</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;">{{ $travelRequest->personal_description }}</div>
                    </div>
                    @endif
                    
                    @if($travelRequest->additional_details)
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Additional Details</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;">{{ $travelRequest->additional_details }}</div>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-check text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Acting Staff (Covering Duties)</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Name</div>
                            <div class="fw-semibold" style="font-size: 0.75rem;">
                                {{ $travelRequest->actingStaff->first_name }} {{ $travelRequest->actingStaff->last_name }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted" style="font-size: 0.6rem;">Status</div>
                            <div>
                                @if($travelRequest->acting_status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Awaiting Response</span>
                                @elseif($travelRequest->acting_status == 'accepted')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Accepted</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Declined</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($travelRequest->acting_responded_at)
                    <div class="mt-1">
                        <div class="text-muted" style="font-size: 0.6rem;">Responded At</div>
                        <div style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($travelRequest->acting_responded_at)->format('d M Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Request Status</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center gap-2 py-1 px-2">
                            <div class="rounded-circle bg-{{ $travelRequest->acting_status == 'accepted' ? 'success' : ($travelRequest->acting_status == 'pending' ? 'warning' : 'danger') }}-subtle p-1" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-{{ $travelRequest->acting_status == 'accepted' ? 'check' : ($travelRequest->acting_status == 'pending' ? 'clock' : 'x') }}" style="font-size: 0.7rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size: 0.7rem;">Acting Staff Response</div>
                                <div class="text-muted" style="font-size: 0.6rem;">
                                    {{ ucfirst($travelRequest->acting_status) }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="list-group-item d-flex align-items-center gap-2 py-1 px-2">
                            @php
                                $statusIconColor = match($travelRequest->status) {
                                    'approved' => 'success',
                                    'pending' => 'warning',
                                    'cancelled' => 'secondary',
                                    default => 'danger',
                                };
                                $statusIcon = match($travelRequest->status) {
                                    'approved' => 'check',
                                    'pending' => 'clock',
                                    'cancelled' => 'ban',
                                    default => 'x',
                                };
                            @endphp
                            <div class="rounded-circle bg-{{ $statusIconColor }}-subtle p-1" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-{{ $statusIcon }}" style="font-size: 0.7rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size: 0.7rem;">Final Approval Status</div>
                                <div class="text-muted" style="font-size: 0.6rem;">
                                    {{ ucfirst($travelRequest->status) }}
                                    @if($travelRequest->current_approval_level != 'completed' && $travelRequest->status == 'pending')
                                        - Awaiting {{ ucfirst(str_replace('_', ' ', $travelRequest->current_approval_level)) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-live-refresh :url="route('travel-permissions.show.heartbeat', $travelRequest->id)" />

<script>
function confirmCancelTravel() {
    confirmAction('Are you sure you want to cancel this travel request? This action cannot be undone.', function () {
        document.getElementById('cancel-travel-form').submit();
    }, { title: 'Cancel Travel Request' });
}
</script>
@endsection