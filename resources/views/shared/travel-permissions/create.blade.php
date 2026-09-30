@extends('layouts.master')

@section('title', 'Apply for Travel Permission')

@push('styles')
<style>
/* Same convention as the Leave application form - each part of the form
   reads as its own card, colored by a thin left accent rather than a heavy
   filled header. */
.form-section-card {
    background: #fff;
    border: 1px solid #E5E3DB;
    border-left-width: 3px;
    border-left-color: var(--section-accent, #94A3B8);
    border-radius: 8px;
    margin-bottom: 0.6rem;
    overflow: hidden;
}
.form-section-card__header {
    padding: 0.45rem 0.65rem 0.35rem;
    border-bottom: 1px solid #EFEDE6;
    font-size: 0.7rem;
    font-weight: 600;
}
.form-section-card__body { padding: 0.55rem 0.65rem 0.6rem; }
</style>
@endpush

@php
    $fd = ($draft->form_data ?? []) ?: [];
    $val = fn ($key, $default = '') => old($key, $fd[$key] ?? $default);
    $currentStep = $errors->any() ? 1 : ($draft->current_step ?? 1);
@endphp

@section('content')
<div class="container-fluid px-2">

    <x-breadcrumb :items="[
        ['name' => 'Travel Permissions', 'url' => route('travel-permissions.my-requests')],
        ['name' => 'Apply for Travel', 'url' => '#']
    ]" />

    <x-page-header title="APPLY FOR TRAVEL PERMISSION" description="Request official or personal travel authorization">
        <a href="{{ route('travel-permissions.my-requests') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>

    @if($errors->any())
    <x-alert-danger :session="false">
    <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
</x-alert-danger>
    @endif

    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-plane text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Travel Application</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">

                    <x-leave-wizard-steps :currentStep="$currentStep" />

                    @if($draft)
                    <div class="alert alert-info py-1 px-2 mb-2 d-flex align-items-center justify-content-between" style="font-size: 0.65rem;">
                        <span><i class="ti ti-clock-hour-4 me-1"></i> You have an unfinished travel request (last saved {{ $draft->updated_at->diffForHumans() }}).</span>
                        <button type="submit" form="travelForm" formaction="{{ route('travel-permissions.draft.discard') }}" formnovalidate class="btn btn-link btn-sm p-0 text-danger" style="font-size: 0.65rem;">Start Over</button>
                    </div>
                    @endif

                    <form action="{{ route('travel-permissions.store') }}" method="POST" enctype="multipart/form-data" id="travelForm">
                        @csrf

                        <div id="step-1-fill" style="{{ $currentStep == 1 ? '' : 'display:none;' }}">

                        <!-- ============================================ -->
                        <!-- TRAVEL DETAILS: destination, dates, reason, cover -->
                        <!-- ============================================ -->
                        <div class="form-section-card" style="--section-accent: #5B84C4;">
                        <div class="form-section-card__header">
                            <i class="ti ti-plane text-primary me-1"></i> Travel Details
                        </div>
                        <div class="form-section-card__body">

                        <!-- Destination -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Destination <span class="text-danger">*</span></label>
                            <input type="text" name="destination" id="destination" class="form-control form-control-sm rounded-sm" value="{{ $val('destination') }}" placeholder="e.g., Nairobi, Kenya" style="font-size: 0.7rem;" required>
                        </div>

                        <!-- Date Range -->
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">From Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control form-control-sm rounded-sm" value="{{ $val('start_date') }}" style="font-size: 0.7rem;" required>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">To Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm rounded-sm" value="{{ $val('end_date') }}" style="font-size: 0.7rem;" required>
                            </div>
                        </div>

                        <!-- Days Display -->
                        <div class="mb-1">
                            <x-alert-info>
    <strong>Number of days:</strong> <span id="days_count">0</span> day(s)
</x-alert-info>
                        </div>

                        <!-- Travel Reason (Official/Personal) with Conditional Fields -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Reason for Travel <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input type="radio" name="travel_reason" id="reason_official" value="official" class="form-check-input" {{ $val('travel_reason') == 'official' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="reason_official" style="font-size: 0.7rem;">Official (University Business)</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="travel_reason" id="reason_personal" value="personal" class="form-check-input" {{ $val('travel_reason') == 'personal' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="reason_personal" style="font-size: 0.7rem;">Personal (Private Travel)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Official Benefit (shown when official selected) -->
                        <div id="official_block" class="mb-1" style="display: none;">
                            <label class="form-label" style="font-size: 0.65rem;">What benefit will the University derive? <span class="text-danger">*</span></label>
                            <textarea name="official_benefit" id="official_benefit" rows="3" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" placeholder="Explain how the University will benefit from this travel...">{{ $val('official_benefit') }}</textarea>
                        </div>

                        <!-- Personal Description (shown when personal selected) -->
                        <div id="personal_block" class="mb-1" style="display: none;">
                            <label class="form-label" style="font-size: 0.65rem;">Description for Official Records <span class="text-danger">*</span></label>
                            <textarea name="personal_description" id="personal_description" rows="3" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" placeholder="Provide a brief description for official records...">{{ $val('personal_description') }}</textarea>
                        </div>

                        <!-- Evidence reminder - the actual upload lives at the final Sign &
                             Submit step (see below), not here, because Next/Back on this
                             wizard reload the page to save the draft and a chosen file can
                             never survive that reload. Flagging it here, right where the
                             reason is written, so it isn't a surprise later. -->
                        <div id="evidence_reminder" class="mb-1" style="display: none;">
                            <small class="text-muted" style="font-size: 0.6rem;"><i class="ti ti-paperclip me-1"></i>You'll attach the invitation letter/evidence and endorsement letter at the final <strong>Sign &amp; Submit</strong> step.</small>
                        </div>

                        <!-- Cost Coverage -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Cost of attendance/travel is covered by <span class="text-danger">*</span></label>
                            <select name="cost_coverage" id="cost_coverage" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                <option value="">Select coverage</option>
                                <option value="organizers_full" {{ $val('cost_coverage') == 'organizers_full' ? 'selected' : '' }}>Organizers (Full Paid)</option>
                                <option value="organizers_partial" {{ $val('cost_coverage') == 'organizers_partial' ? 'selected' : '' }}>Organizers (Partially Paid)</option>
                                <option value="university" {{ $val('cost_coverage') == 'university' ? 'selected' : '' }}>University</option>
                                <option value="private" {{ $val('cost_coverage') == 'private' ? 'selected' : '' }}>Private Travel</option>
                            </select>
                        </div>

                        <!-- Acting Staff -->
                        <div class="mb-1">
                            @if($colleagues->isEmpty())
                                <label class="form-label" style="font-size: 0.65rem;">Who will cover your duties?</label>
                                <x-alert-warning>
    No colleague in your department is currently available to cover your duties.
                                    @if($escalationContact)
                                        <strong>{{ $escalationContact->first_name }} {{ $escalationContact->last_name }}</strong> will be notified automatically to arrange coverage, including from another department if needed.
                                    @endif
</x-alert-warning>
                            @else
                            <label class="form-label" style="font-size: 0.65rem;">Who will cover your duties? <span class="text-danger">*</span></label>
                            <select name="acting_staff_id" id="acting_staff_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                <option value="">Select Colleague</option>
                                @foreach($colleagues as $colleague)
                                <option value="{{ $colleague->id }}" {{ $val('acting_staff_id') == $colleague->id ? 'selected' : '' }}>
                                    {{ $colleague->first_name }} {{ $colleague->last_name }} ({{ $colleague->staff_id }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.55rem;">This person will be notified to accept covering your duties</small>
                            @endif
                        </div>

                        <!-- Acting Dean (only shown to a Dean applying for their own travel) -->
                        @if(isset($actingDeanOptions) && $actingDeanOptions->isNotEmpty())
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Who will act as Dean to review this request? <span class="text-danger">*</span></label>
                            <select name="acting_dean_id" id="acting_dean_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                <option value="">Select HOD</option>
                                @foreach($actingDeanOptions as $hodOption)
                                <option value="{{ $hodOption->id }}" {{ $val('acting_dean_id') == $hodOption->id ? 'selected' : '' }}>
                                    {{ $hodOption->title ? $hodOption->title . ' ' : '' }}{{ $hodOption->first_name }} {{ $hodOption->last_name }} ({{ $hodOption->staff_id }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.55rem;">As Dean, your own request needs someone else to review it at the Dean stage - pick an HOD from your school to stand in.</small>
                        </div>
                        @endif

                        </div><!-- /.form-section-card__body -->
                        </div><!-- /Travel Details -->

                        {{-- Kept out of the way until Travel Details' required fields are
                             filled, so the form doesn't overwhelm with every section at
                             once - see refreshFormSections() in the page script. --}}
                        <div id="tier2_group" style="display:none;">

                        <!-- Handover / Coverage Notes -->
                        <div class="form-section-card" style="--section-accent: #198754;">
                        <div class="form-section-card__header">
                            <i class="ti ti-file-description text-success me-1"></i> Handover / Coverage Notes <span class="text-danger">*</span>
                        </div>
                        <div class="form-section-card__body">
                            <x-numbered-letter-field
                                name="handover_notes"
                                id="handover_notes"
                                :showLabel="false"
                                :required="true"
                                :rows="3"
                                :value="$val('handover_notes')"
                                placeholder="Describe your pending tasks and who will cover them during your travel..."
                                helpText="Be specific about what needs attention while you're away - whoever covers for you will read this."
                            />
                        </div><!-- /.form-section-card__body -->
                        </div><!-- /Handover / Coverage Notes -->

                        <!-- Additional Details -->
                        <div class="form-section-card" style="--section-accent: #0dcaf0;">
                        <div class="form-section-card__header">
                            <i class="ti ti-notes text-info me-1"></i> Additional Details (Optional)
                        </div>
                        <div class="form-section-card__body">
                            <textarea name="additional_details" rows="2" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" placeholder="Any other relevant information...">{{ $val('additional_details') }}</textarea>
                        </div><!-- /.form-section-card__body -->
                        </div><!-- /Additional Details -->

                        <!-- Letter Content (Optional) -->
                        <div class="form-section-card" style="--section-accent: #0d6efd;">
                        <div class="form-section-card__header">
                            <i class="ti ti-file-text text-primary me-1"></i> Letter Content (Optional)
                        </div>
                        <div class="form-section-card__body">
                            <x-numbered-letter-field
                                name="letter_content"
                                :showLabel="false"
                                :rows="5"
                                :value="$val('letter_content')"
                                placeholder="Write your travel permission letter here..."
                                helpText="This becomes part of your formal travel application letter, in your own words - entirely optional."
                            />
                        </div><!-- /.form-section-card__body -->
                        </div><!-- /Letter Content -->

                        </div><!-- /#tier2_group -->

                        </div><!-- /#step-1-fill -->

                        <!-- ============================================ -->
                        <!-- STEP 2: REVIEW -->
                        <!-- ============================================ -->
                        <div id="step-2-review" style="{{ $currentStep == 2 ? '' : 'display:none;' }}">
                            <x-alert-info class="mb-2">
                                Check everything below before signing. Use <strong>Back</strong> to change anything.
                            </x-alert-info>

                            <div class="table-responsive mb-2">
                                <table class="table table-sm" style="font-size: 0.68rem;">
                                    <tbody>
                                        <tr><td class="text-muted" style="width: 40%;">Destination</td><td>{{ $val('destination') ?: '—' }}</td></tr>
                                        <tr><td class="text-muted">Dates</td><td>{{ $val('start_date') ?: '—' }} &rarr; {{ $val('end_date') ?: '—' }}</td></tr>
                                        <tr><td class="text-muted">Reason</td><td>{{ $val('travel_reason') ? ucfirst($val('travel_reason')) : '—' }}</td></tr>
                                        <tr><td class="text-muted">Acting Staff</td><td>{{ optional($colleagues->firstWhere('id', (int) $val('acting_staff_id')))->full_name ?? '—' }}</td></tr>
                                        <tr><td class="text-muted">Handover / Coverage Notes</td><td style="white-space: pre-line;">{{ $val('handover_notes') ?: '—' }}</td></tr>
                                        <tr><td class="text-muted">Letter Content</td><td style="white-space: pre-line;">{{ $val('letter_content') ?: '—' }}</td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-file-text text-success me-1"></i> Your Travel Application Letter (on the real letterhead)
                            </div>
                            <div class="border rounded-sm" style="overflow: hidden;">
                                <iframe src="{{ route('travel-permissions.draft.preview') }}" style="width: 100%; height: 560px; border: 0;"></iframe>
                            </div>
                        </div>

                        <!-- ============================================ -->
                        <!-- STEP 3: SIGN & SUBMIT -->
                        <!-- ============================================ -->
                        <div id="step-3-sign" style="{{ $currentStep == 3 ? '' : 'display:none;' }}">
                            <x-alert-info class="mb-2">
                                Sign below to confirm the letter shown in Review is yours. Signing is optional, but recommended if you wrote a letter.
                            </x-alert-info>
                            <x-signature-pad
                                name="signature_data"
                                label="Your Signature"
                                :required="false"
                                helpText="Draw with mouse or touch - this appears on your Travel Application Letter."
                            />

                            <!-- Evidence Attachments - selected here, right before submit, so nothing
                                 is lost across the Fill/Review page reloads. -->
                            <div class="form-section-card mt-2" style="--section-accent: #dc3545;">
                            <div class="form-section-card__header">
                                <i class="ti ti-file-text text-danger me-1"></i> Supporting Documents
                            </div>
                            <div class="form-section-card__body">
                                <div class="mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Invitation Letter / Evidence</label>
                                    <input type="file" name="invitation_letter" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted" style="font-size: 0.55rem;">Attach invitation letter or evidence of travel (Max 5MB)</small>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Endorsement Letter</label>
                                    <input type="file" name="endorsement" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted" style="font-size: 0.55rem;">Attach letter of endorsement (if applicable)</small>
                                </div>
                            </div><!-- /.form-section-card__body -->
                            </div><!-- /Supporting Documents -->
                        </div>

                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('travel-permissions.my-requests') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>

                            @if($currentStep == 1)
                                <button type="submit" id="next_review_button" formaction="{{ route('travel-permissions.draft.save') }}" formnovalidate name="current_step" value="2" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;" disabled>
                                    Next: Review <i class="ti ti-arrow-right ms-1"></i>
                                </button>
                            @elseif($currentStep == 2)
                                <button type="submit" formaction="{{ route('travel-permissions.draft.save') }}" formnovalidate name="current_step" value="1" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                    <i class="ti ti-arrow-left me-1"></i> Back
                                </button>
                                <button type="submit" formaction="{{ route('travel-permissions.draft.save') }}" formnovalidate name="current_step" value="3" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                    Next: Sign <i class="ti ti-arrow-right ms-1"></i>
                                </button>
                            @else
                                <button type="submit" formaction="{{ route('travel-permissions.draft.save') }}" formnovalidate name="current_step" value="2" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                    <i class="ti ti-arrow-left me-1"></i> Back
                                </button>
                                <button type="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem;">
                                    <i class="ti ti-send me-1"></i> Submit Request
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Approval Process</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">1. Select colleague to cover duties</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">2. Colleague accepts/declines</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">3. HOD reviews</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">4. Dean reviews (academic staff)</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">5. DVC authorizes</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">6. HR final approval</small>
                            </div>

</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Calculate days between dates
function calculateDays() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);

        if (end >= start) {
            let days = 0;
            let current = new Date(start);
            while (current <= end) {
                const dayOfWeek = current.getDay();
                if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                    days++;
                }
                current.setDate(current.getDate() + 1);
            }
            document.getElementById('days_count').innerText = days;
        } else {
            document.getElementById('days_count').innerText = '0';
        }
    }
}

// Show/hide conditional fields based on travel reason
function toggleReasonFields() {
    const officialRadio = document.getElementById('reason_official');
    const personalRadio = document.getElementById('reason_personal');
    const officialBlock = document.getElementById('official_block');
    const personalBlock = document.getElementById('personal_block');
    const evidenceReminder = document.getElementById('evidence_reminder');

    if (officialRadio.checked) {
        officialBlock.style.display = 'block';
        personalBlock.style.display = 'none';
        evidenceReminder.style.display = 'block';
    } else if (personalRadio.checked) {
        officialBlock.style.display = 'none';
        personalBlock.style.display = 'block';
        evidenceReminder.style.display = 'block';
    } else {
        officialBlock.style.display = 'none';
        personalBlock.style.display = 'none';
        evidenceReminder.style.display = 'none';
    }
}

document.getElementById('start_date').addEventListener('change', calculateDays);
document.getElementById('end_date').addEventListener('change', calculateDays);
document.getElementById('reason_official').addEventListener('change', toggleReasonFields);
document.getElementById('reason_personal').addEventListener('change', toggleReasonFields);

// Initial calculation and toggle
calculateDays();
toggleReasonFields();

// Progressive reveal: the form opens with only Travel Details showing, so a
// long form doesn't overwhelm someone who's only just started - the
// optional sections below it (Handover/Coverage Notes, Additional Details,
// Letter Content) appear together once Travel Details' own required fields
// are filled. A resumed draft that already has those values reveals the
// rest immediately, rather than making them retype anything.
function refreshFormSections() {
    const val = id => (document.getElementById(id)?.value || '').trim();
    const travelReason = document.querySelector('input[name="travel_reason"]:checked')?.value;
    const actingStaffEl = document.getElementById('acting_staff_id');
    const actingDeanEl = document.getElementById('acting_dean_id');

    const travelDetailsDone = !!(
        val('destination')
        && val('start_date')
        && val('end_date')
        && travelReason
        && (travelReason === 'official' ? val('official_benefit') : val('personal_description'))
        && val('cost_coverage')
        && (!actingStaffEl || actingStaffEl.value)
        && (!actingDeanEl || actingDeanEl.value)
    );

    const tier2 = document.getElementById('tier2_group');
    if (tier2) {
        tier2.style.display = travelDetailsDone ? '' : 'none';
    }

    // Handover/Coverage Notes is required, but only reachable once Travel
    // Details itself is done (it lives inside tier2_group) - so the Next
    // button stays disabled until both are satisfied, same gating pattern
    // as Leave's own Handover Notes -> Next: Review chain.
    const handoverDone = travelDetailsDone && val('handover_notes').length >= 5;
    const nextButton = document.getElementById('next_review_button');
    if (nextButton) {
        nextButton.disabled = !handoverDone;
    }
}

[
    'destination', 'start_date', 'end_date', 'official_benefit', 'personal_description',
    'cost_coverage', 'acting_staff_id', 'acting_dean_id', 'handover_notes',
].forEach(id => document.getElementById(id)?.addEventListener('input', refreshFormSections));
[
    'destination', 'start_date', 'end_date', 'cost_coverage', 'acting_staff_id', 'acting_dean_id',
].forEach(id => document.getElementById(id)?.addEventListener('change', refreshFormSections));
document.getElementById('reason_official')?.addEventListener('change', refreshFormSections);
document.getElementById('reason_personal')?.addEventListener('change', refreshFormSections);
refreshFormSections();
</script>
@endsection
