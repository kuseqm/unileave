@extends('layouts.master')

@section('title', 'Acting Response')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card rounded-sm" style="max-width: 500px;">
        <div class="card-header py-2 px-3 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-plane text-primary me-2"></i>
                <span style="font-size: 0.8rem; font-weight: 600;">Travel Permission - Acting Request</span>
            </div>
        </div>
        <div class="card-body p-3">
            <h6 class="mb-2">{{ $travelRequest->staff->first_name }} {{ $travelRequest->staff->last_name }} requests you to cover their duties</h6>
            <p><strong>Destination:</strong> {{ $travelRequest->destination }}</p>
            <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($travelRequest->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($travelRequest->end_date)->format('d M Y') }}</p>

            @if($travelRequest->handover_notes)
            <div class="bg-light p-2 rounded-sm mb-2">
                <strong style="font-size: 0.75rem;"><i class="ti ti-file-description text-success me-1"></i> What you'll be covering:</strong>
                <div class="mt-1" style="font-size: 0.75rem;">
                    <x-numbered-paragraphs :text="$travelRequest->handover_notes" />
                </div>
            </div>
            @endif

            <form id="actingResponseForm" method="POST" action="{{ route('travel-permissions.acting.process', $travelRequest->id) }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label">Comment (Optional)</label>
                    <textarea name="comment" class="form-control form-control-sm rounded-sm" rows="2"></textarea>
                </div>
                <div class="d-flex gap-2">
                    <input type="hidden" name="action" id="action_value">
                    <button type="button" class="btn btn-success btn-sm flex-fill" onclick="submitResponse('accept')">Accept</button>
                    <button type="button" class="btn btn-danger btn-sm flex-fill" onclick="submitResponse('decline')">Decline</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function submitResponse(action) {
    document.getElementById('action_value').value = action;
    // Not document.querySelector('form') - the layout's hidden logout form
    // appears earlier in the DOM and would be submitted instead, logging
    // the acting staff out rather than recording their response.
    document.getElementById('actingResponseForm').submit();
}
</script>
@endsection