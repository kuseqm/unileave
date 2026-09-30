@extends('layouts.master')

@section('title', 'Acting Staff Response')

@section('content')
<div class="container-fluid px-2">

    <x-breadcrumb :items="[
        ['name' => 'Hold Position Request', 'url' => '#']
    ]" />

    <x-page-header title="HOLD POSITION REQUEST" description="Respond to this acting-staff request" />

    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-check text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Hold Position Request</span>
                    </div>
                </div>
                <div class="card-body py-3 px-3">
                    <p class="mb-2" style="font-size: 0.8rem;"><strong>{{ $leaveRequest->staff->first_name }} {{ $leaveRequest->staff->last_name }}</strong> has requested you to hold their position during their leave.</p>

                    <x-alert-light class="py-2 px-2 mb-3 rounded-sm">

                        <div class="small">
                            <strong>Leave Details:</strong><br>
                            Type: {{ $leaveRequest->leaveType->name ?? 'N/A' }}<br>
                            Duration: {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }} ({{ $leaveRequest->days }} days)<br>
                            Reason: {{ $leaveRequest->reason }}
                        </div>

</x-alert-light>

                    @if($leaveRequest->handover_notes)
                    <x-alert-light class="py-2 px-2 mb-3 rounded-sm">

                        <div class="small">
                            <strong><i class="ti ti-file-description text-success me-1"></i> What you'll be taking on:</strong>
                            <div class="mt-1">
                                <x-numbered-paragraphs :text="$leaveRequest->handover_notes" />
                            </div>
                        </div>

</x-alert-light>
                    @endif

                    <form id="actingResponseForm" action="{{ route('leave-requests.acting.process', $leaveRequest->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" id="action" value="">

                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.7rem;">Comment (Optional)</label>
                            <textarea name="comment" class="form-control form-control-sm rounded-sm" rows="2" style="font-size: 0.7rem;"></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success btn-sm rounded-sm" onclick="submitAction('accept')" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-check me-1"></i> Accept &amp; Hold Position
                            </button>
                            <button type="button" class="btn btn-danger btn-sm rounded-sm" onclick="submitAction('decline')" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-x me-1"></i> Decline
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-check me-1 text-success"></i> If you accept</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>You'll cover {{ $leaveRequest->staff->first_name }}'s position for the leave dates above</li>
                            <li>The request moves on to Head of Department review</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-x me-1 text-danger"></i> If you decline</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>{{ $leaveRequest->staff->first_name }} is notified right away</li>
                            <li>They'll need to pick another colleague before the request can proceed</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Note</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">A comment is optional either way, but helps explain your decision.</small>
                            </div>
                        
</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function submitAction(action) {
        document.getElementById('action').value = action;
        // Not document.querySelector('form') - the layout's hidden logout
        // form appears earlier in the DOM and would be submitted instead,
        // logging the acting staff out rather than recording their response.
        document.getElementById('actingResponseForm').submit();
    }
</script>
@endsection
