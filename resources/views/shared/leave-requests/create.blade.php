@extends('layouts.master')

@section('title', 'Apply for Leave')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
/* Match the rest of the form's compact sizing - Select2's defaults are taller/larger. */
.select2-container .select2-selection--single { height: calc(1.5em + 0.5rem + 2px); font-size: 0.7rem; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height: calc(1.5em + 0.4rem); padding-left: 0.3rem; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height: calc(1.5em + 0.5rem); }
.select2-dropdown { font-size: 0.7rem; }

/* Each part of the form reads as its own card, colored by a thin left
   accent rather than a heavy filled header - the header itself stays a
   plain label + icon on white so it doesn't read as a second toolbar. */
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
    // The wizard (draft/review/sign) is a self-service feature - submitting
    // on behalf of someone else keeps using the original single-page form,
    // since a draft can only ever belong to the person who's logged in.
    $isWizard = ! isset($onBehalfStaff);
    $fd = ($draft->form_data ?? []) ?: [];
    $val = fn ($key, $default = '') => old($key, $fd[$key] ?? $default);
    $currentStep = $errors->any() ? 1 : ($draft->current_step ?? 1);
@endphp

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Leave Requests', 'url' => route('leave-requests.my-requests')],
        ['name' => 'Apply for Leave', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="APPLY FOR LEAVE" description="Submit a new leave request">
        <a href="{{ route('leave-requests.my-requests') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>

    <!-- Validation Errors -->
    @if($errors->any())
    <x-alert-danger :session="false" :dismissible="true" icon="ti-alert-circle">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert-danger>
    @endif

    <!-- Success Message -->
    <x-alert-success />

    <!-- Error Message -->
    <x-alert-danger />

    <div class="row g-2">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Leave Application</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">

                    @if($isWizard)
                        <x-leave-wizard-steps :currentStep="$currentStep" />

                        @if($draft)
                        <div class="alert alert-info py-1 px-2 mb-2 d-flex align-items-center justify-content-between" style="font-size: 0.65rem;">
                            <span><i class="ti ti-clock-hour-4 me-1"></i> You have an unfinished leave request (last saved {{ $draft->updated_at->diffForHumans() }}).</span>
                            <button type="submit" form="leaveForm" formaction="{{ route('leave-requests.draft.discard') }}" formnovalidate class="btn btn-link btn-sm p-0 text-danger" style="font-size: 0.65rem;">Start Over</button>
                        </div>
                        @endif
                    @endif

                    <form action="{{ route('leave-requests.store') }}" method="POST" id="leaveForm" enctype="multipart/form-data">
                        @csrf

                        @if(isset($onBehalfStaff))
                        <div class="alert alert-warning py-1 px-2 mb-2" style="font-size: 0.7rem;">
                            You are submitting this leave on behalf of <strong>{{ $onBehalfStaff->full_name }} ({{ $onBehalfStaff->staff_id }})</strong>.
                        </div>
                        <input type="hidden" name="behalf_staff_id" value="{{ $onBehalfStaff->id }}">
                        <input type="hidden" name="staff_id" value="{{ $onBehalfStaff->id }}">
                        @endif

                        <div id="step-1-fill" style="{{ (!$isWizard || $currentStep == 1) ? '' : 'display:none;' }}">

                        <!-- ============================================ -->
                        <!-- LEAVE DETAILS: type, dates, who holds your position -->
                        <!-- ============================================ -->
                        <div class="form-section-card" style="--section-accent: #0d6efd;">
                        <div class="form-section-card__header">
                            <i class="ti ti-calendar-event text-primary me-1"></i> Leave Details
                        </div>
                        <div class="form-section-card__body">

                        <!-- Leave Type -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Leave Type <span class="text-danger">*</span></label>
                            <select name="leave_type_id" id="leave_type_id" class="form-select form-select-sm rounded-sm @error('leave_type_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select Leave Type</option>
                                @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ $val('leave_type_id') == $type->id ? 'selected' : '' }} data-requires-cert="{{ $type->requires_certificate }}" data-code="{{ $type->code }}">
                                    {{ $type->name }} ({{ $type->code }}) @if($type->requires_certificate) - Requires Certificate @endif
                                </option>
                                @endforeach
                            </select>
                            @error('leave_type_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                            <small id="leave_type_hint" class="text-muted d-block" style="font-size: 0.6rem;"></small>
                        </div>

                        <!-- Date Range -->
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control form-control-sm rounded-sm @error('start_date') is-invalid @enderror" value="{{ $val('start_date') }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                @error('start_date')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                                <small id="start_date_hint" class="text-muted d-block" style="font-size: 0.6rem;"></small>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm rounded-sm @error('end_date') is-invalid @enderror" value="{{ $val('end_date') }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                @error('end_date')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                                <small id="end_date_hint" class="text-muted d-block" style="font-size: 0.6rem;"></small>
                            </div>
                        </div>

                        <!-- Half day: shown only where the university's policy allows it, for a single day -->
                        <div class="mb-1" id="half_day_wrap" style="display: none;">
                            <label class="form-label" style="font-size: 0.65rem;">Duration</label>
                            <select name="half_day" id="half_day" class="form-select form-select-sm rounded-sm @error('half_day') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                <option value="full" {{ $val('half_day', 'full') === 'full' ? 'selected' : '' }}>Full day(s)</option>
                                <option value="morning" {{ $val('half_day') === 'morning' ? 'selected' : '' }}>Half day - morning</option>
                                <option value="afternoon" {{ $val('half_day') === 'afternoon' ? 'selected' : '' }}>Half day - afternoon</option>
                            </select>
                            @error('half_day')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                            <small id="half_day_hint" class="text-muted d-block" style="font-size: 0.6rem;"></small>
                        </div>

                        <!-- Expected Return Date -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Expected Return Date</label>
                            <input type="date" name="expected_return_date" id="expected_return_date" class="form-control form-control-sm rounded-sm @error('expected_return_date') is-invalid @enderror" value="{{ $val('expected_return_date') }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                            @error('expected_return_date')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                            <small id="return_date_hint" class="text-muted" style="font-size: 0.55rem;">When do you plan to resume work?</small>
                        </div>

                        <!-- Days Display -->
                        <div class="mb-1">
                            <x-alert-info>
                                <strong>Number of days:</strong> <span id="days_count">0</span> day(s)
                            </x-alert-info>
                        </div>

                        <!-- Acting Staff (Hold Position) -->
                        <div class="mb-1">
                            @if($colleagues->isEmpty())
                                <label class="form-label" style="font-size: 0.65rem;">Who will hold your position?</label>
                                <x-alert-warning>
                                    No colleague in your department is currently available to hold your position.
                                    @if($escalationContact)
                                        <strong>{{ $escalationContact->first_name }} {{ $escalationContact->last_name }}</strong> will be notified automatically to arrange coverage, including from another department if needed.
                                    @endif
                                </x-alert-warning>
                            @else
                            <label class="form-label" style="font-size: 0.65rem;">Who will hold your position? <span class="text-danger">*</span></label>
                            <select name="acting_staff_id" id="acting_staff_id" class="form-select form-select-sm rounded-sm @error('acting_staff_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem; width: 100%;" required>
                                <option value="">Select Colleague</option>
                                @foreach($colleagues as $colleague)
                                <option value="{{ $colleague->id }}" {{ $val('acting_staff_id') == $colleague->id ? 'selected' : '' }}>
                                    {{ $colleague->title ? $colleague->title . ' ' : '' }}{{ $colleague->first_name }} @if($colleague->middle_name){{ $colleague->middle_name }} @endif{{ $colleague->last_name }} ({{ $colleague->staff_id }})
                                </option>
                                @endforeach
                            </select>
                            @error('acting_staff_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" style="font-size: 0.6rem;">This person will be notified to accept or decline holding your position during your absence.</small>
                            @endif
                        </div>

                        <!-- Acting Dean (only shown to a Dean applying for their own leave) -->
                        @if(isset($actingDeanOptions) && $actingDeanOptions->isNotEmpty())
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Who will act as Dean to review this request? <span class="text-danger">*</span></label>
                            <select name="acting_dean_id" id="acting_dean_id" class="form-select form-select-sm rounded-sm @error('acting_dean_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem; width: 100%;" required>
                                <option value="">Select HOD</option>
                                @foreach($actingDeanOptions as $hodOption)
                                <option value="{{ $hodOption->id }}" {{ $val('acting_dean_id') == $hodOption->id ? 'selected' : '' }}>
                                    {{ $hodOption->title ? $hodOption->title . ' ' : '' }}{{ $hodOption->first_name }} {{ $hodOption->last_name }} ({{ $hodOption->staff_id }})
                                </option>
                                @endforeach
                            </select>
                            @error('acting_dean_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" style="font-size: 0.6rem;">As Dean, your own request needs someone else to review it at the Dean stage - pick an HOD from your school to stand in.</small>
                        </div>
                        @endif

                        </div><!-- /.form-section-card__body -->
                        </div><!-- /Leave Details -->

                        {{-- Kept out of the way until Leave Details' required fields are
                             filled, so the form doesn't overwhelm with every section at
                             once - see refreshFormSections() in the page script. Each
                             further group only reveals once the one before it is done. --}}
                        <div id="tier_emergency" style="display:none;">
                        @if(!isset($onBehalfStaff))
                        <!-- ============================================ -->
                        <!-- EMERGENCY CONTACT SECTION -->
                        <!-- ============================================ -->
                        <div class="form-section-card" style="--section-accent: #ffc107;">
                        <div class="form-section-card__header">
                            <i class="ti ti-phone text-warning me-1"></i> Emergency Contact <span class="text-danger">*</span>
                        </div>
                        <div class="form-section-card__body">

                            <!-- Select from saved contacts -->
                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Select Saved Emergency Contact</label>
                                <select name="emergency_contact_id" id="emergency_contact_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                    <option value="">-- Select from saved contacts --</option>
                                    @foreach(auth()->user()->emergencyContacts ?? [] as $contact)
                                    <option value="{{ $contact->id }}" data-name="{{ $contact->full_name }}" data-phone="{{ $contact->phone_primary }}">
                                        {{ $contact->full_name }} ({{ $contact->relationship }}) - {{ $contact->phone_primary }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-muted" style="font-size: 0.55rem;">Or manually enter below</small>
                            </div>

                            <!-- Manual Emergency Contact -->
                            <div class="row g-1">
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Emergency Contact Name <span class="text-danger">*</span></label>
                                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control form-control-sm rounded-sm" value="{{ $val('emergency_contact_name') }}" style="font-size: 0.7rem;" placeholder="Full name" required>
                                </div>
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Emergency Contact Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control form-control-sm rounded-sm" value="{{ $val('emergency_contact_phone') }}" style="font-size: 0.7rem;" placeholder="Phone number" required>
                                </div>
                            </div>

                        </div><!-- /.form-section-card__body -->
                        </div><!-- /Emergency Contact -->
                        @endif
                        </div><!-- /#tier_emergency -->

                        {{-- Revealed once Emergency Contact is filled (or immediately, for
                             an on-behalf submission, since that card doesn't exist there). --}}
                        <div id="tier_contact" style="display:none;">

                        <!-- ============================================ -->
                        <!-- CONTACT DURING LEAVE & ADDRESS -->
                        <!-- ============================================ -->
                        <div class="form-section-card" style="--section-accent: #0dcaf0;">
                        <div class="form-section-card__header">
                            <i class="ti ti-map-pin text-info me-1"></i> Contact Information During Leave <span class="text-danger">*</span>
                        </div>
                        <div class="form-section-card__body">

                            <div class="row g-1">
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Contact Number (During Leave) <span class="text-danger">*</span></label>
                                    <input type="text" name="contact_number_during_leave" id="contact_number_during_leave" class="form-control form-control-sm rounded-sm" value="{{ $val('contact_number_during_leave') }}" style="font-size: 0.7rem;" placeholder="Phone number where you can be reached" required>
                                </div>
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Leave Address <span class="text-danger">*</span></label>
                                    <input type="text" name="leave_address" id="leave_address" class="form-control form-control-sm rounded-sm" value="{{ $val('leave_address') }}" style="font-size: 0.7rem;" placeholder="Where you will be staying" required>
                                </div>
                            </div>

                            <div class="row g-1">
                                <div class="col-md-12 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Travel Destination <span class="text-danger">*</span></label>
                                    <input type="text" name="travel_destination" id="travel_destination" class="form-control form-control-sm rounded-sm" value="{{ $val('travel_destination') }}" style="font-size: 0.7rem;" placeholder="Where are you traveling to" required>
                                </div>
                            </div>

                        </div><!-- /.form-section-card__body -->
                        </div><!-- /Contact Information During Leave -->

                        {{-- Ties to travel_permissions in the data model too - this is a leave
                             REQUEST carrying travel-specific fields, kept neutral in color pending
                             a decision on whether it belongs here or only on Travel Permission.
                             Only relevant for Special Leave (LC5) and University Business/Travel
                             (LC10) - hidden for every other type; applyPolicyLimits() in the page
                             script toggles it from the selected leave type's data-code. --}}
                        <!-- ============================================ -->
                        <!-- OFFICIAL TRAVEL DETAILS -->
                        <!-- ============================================ -->
                        <div class="form-section-card" id="official_travel_details" style="--section-accent: #6c757d; display:none;">
                        <div class="form-section-card__header">
                            <i class="ti ti-briefcase text-secondary me-1"></i> Official Travel Details
                        </div>
                        <div class="form-section-card__body">

                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Benefit to University</label>
                                <textarea name="benefit_to_university" rows="2" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" placeholder="What benefit will the university derive from this travel? (Required for official travel)">{{ $val('benefit_to_university') }}</textarea>
                                <small class="text-muted" style="font-size: 0.55rem;">Explain how this travel benefits SJUT</small>
                            </div>

                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Cost Coverage</label>
                                <select name="cost_coverage" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                    <option value="">Select who covers the cost</option>
                                    <option value="organizers_full" {{ $val('cost_coverage') == 'organizers_full' ? 'selected' : '' }}>Organizers (Fully Paid)</option>
                                    <option value="organizers_partial" {{ $val('cost_coverage') == 'organizers_partial' ? 'selected' : '' }}>Organizers (Partially Paid)</option>
                                    <option value="sjut" {{ $val('cost_coverage') == 'sjut' ? 'selected' : '' }}>SJUT</option>
                                    <option value="private" {{ $val('cost_coverage') == 'private' ? 'selected' : '' }}>Private Travel</option>
                                </select>
                            </div>

                            <div class="form-check">
                                <input type="checkbox" name="is_first_time_applicant" class="form-check-input" id="firstTimeApplicant" value="1" {{ $val('is_first_time_applicant') ? 'checked' : '' }}>
                                <label class="form-check-label" for="firstTimeApplicant" style="font-size: 0.65rem;">
                                    This is my first time applying for this type of leave
                                </label>
                            </div>

                        </div><!-- /.form-section-card__body -->
                        </div><!-- /Official Travel Details -->

                        </div><!-- /#tier_contact -->

                        {{-- Revealed once Contact Information During Leave is filled. --}}
                        <div id="tier_handover" style="display:none;">

                        <!-- ============================================ -->
                        <!-- HANDOVER NOTES -->
                        <!-- ============================================ -->
                        <div class="form-section-card" style="--section-accent: #198754;">
                        <div class="form-section-card__header">
                            <i class="ti ti-file-description text-success me-1"></i> Handover Notes <span class="text-danger">*</span>
                        </div>
                        <div class="form-section-card__body">

                            <x-numbered-letter-field
                                name="handover_notes"
                                id="handover_notes"
                                :showLabel="false"
                                :required="true"
                                :rows="3"
                                :value="$val('handover_notes')"
                                placeholder="Describe your pending tasks, ongoing projects, and who will handle them during your absence..."
                                helpText="Be specific about what needs attention while you're away - whoever holds your position while you're gone will read this."
                            />

                        </div><!-- /.form-section-card__body -->
                        </div><!-- /Handover Notes -->

                        <!-- ============================================ -->
                        <!-- SUPPORTING DOCUMENTS -->
                        <!-- ============================================ -->
                        <div class="form-section-card" style="--section-accent: #dc3545;">
                        <div class="form-section-card__header">
                            <i class="ti ti-file-text text-danger me-1"></i> Supporting Documents
                        </div>
                        <div class="form-section-card__body">

                            {{-- Closed by default and just an optional link for a leave type
                                 that doesn't need a document; forced open (and no longer
                                 optional-looking) once the chosen type does - see
                                 refreshFormSections() for the data-requires-cert check. --}}
                            <details id="supporting_documents_details">
                                <summary id="supporting_documents_summary" style="cursor: pointer; font-size: 0.68rem; color: #555;">
                                    <i class="ti ti-paperclip me-1"></i> Add supporting documents (optional)
                                </summary>
                                <div class="mt-2">
                            @if($isWizard)
                                <div class="mb-1">
                                    <input type="file" id="draftDocumentInput" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="text-muted" style="font-size: 0.55rem;">Uploaded right away (max 5MB) so it's kept even if you come back to this later.</small>
                                    <div id="certificate_requirement" class="text-danger d-none" style="font-size: 0.55rem;">This leave type requires a medical certificate</div>
                                </div>
                                <div id="draftDocumentList" class="mt-1">
                                    @foreach($draftDocuments as $doc)
                                    <div class="d-flex align-items-center gap-1 mb-1" data-doc-id="{{ $doc->id }}" style="font-size: 0.6rem;">
                                        <i class="ti ti-file text-primary"></i>
                                        <span>{{ $doc->original_name }}</span>
                                        <span class="text-muted">({{ round($doc->file_size / 1024, 1) }} KB)</span>
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1 remove-draft-doc" data-id="{{ $doc->id }}">Remove</button>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="mb-1">
                                    <input type="file" name="supporting_documents[]" id="supporting_documents" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="text-muted" style="font-size: 0.55rem;">Upload medical certificates, invitation letters, or other supporting documents (Max 5 files, 5MB each)</small>
                                    <div id="certificate_requirement" class="text-danger d-none" style="font-size: 0.55rem;">This leave type requires a medical certificate</div>
                                </div>
                                <div id="fileList" class="mt-1"></div>
                            @endif
                                </div>
                            </details>

                        </div><!-- /.form-section-card__body -->
                        </div><!-- /Supporting Documents -->

                        </div><!-- /#tier_handover -->

                        {{-- Revealed once Handover Notes has content. --}}
                        <div id="tier_reason" style="display:none;">

                        <!-- Reason -->
                        <div class="form-section-card" style="--section-accent: #212529;">
                        <div class="form-section-card__header">
                            <i class="ti ti-message-2 text-dark me-1"></i> Reason for Your Leave Application <span class="text-danger">*</span>
                        </div>
                        <div class="form-section-card__body">
                            <div class="mb-1">
                                <textarea name="reason" id="reason" rows="3" class="form-control form-control-sm rounded-sm @error('reason') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" placeholder="Briefly explain why you are requesting this leave - this is what your approvers will read first." required>{{ $val('reason') }}</textarea>
                                @error('reason')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" style="font-size: 0.55rem;">At least 10 characters - a short, clear explanation is enough.</small>
                            </div>

                        </div><!-- /.form-section-card__body -->
                        </div><!-- /Reason -->

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
                                placeholder="Write your leave letter here..."
                                helpText="This becomes part of your formal leave letter, in your own words - entirely optional."
                            />

                        </div><!-- /.form-section-card__body -->
                        </div><!-- /Letter Content -->

                        </div><!-- /#tier_reason -->

                        </div><!-- /#step-1-fill -->

                        @if($isWizard)
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
                                        <tr><td class="text-muted" style="width: 40%;">Leave Type</td><td>{{ optional($leaveTypes->firstWhere('id', (int) $val('leave_type_id')))->name ?? '—' }}</td></tr>
                                        <tr><td class="text-muted">Dates</td><td>{{ $val('start_date') ?: '—' }} &rarr; {{ $val('end_date') ?: '—' }}</td></tr>
                                        @if($val('half_day') && $val('half_day') !== 'full')
                                        <tr><td class="text-muted">Duration</td><td>Half day - {{ $val('half_day') }}</td></tr>
                                        @endif
                                        <tr><td class="text-muted">Expected Return</td><td>{{ $val('expected_return_date') ?: '—' }}</td></tr>
                                        <tr><td class="text-muted">Acting Staff</td><td>{{ optional($colleagues->firstWhere('id', (int) $val('acting_staff_id')))->full_name ?? '—' }}</td></tr>
                                        @if(!isset($onBehalfStaff))
                                        <tr><td class="text-muted">Emergency Contact</td><td>{{ $val('emergency_contact_name') ? $val('emergency_contact_name').' ('.$val('emergency_contact_phone').')' : '—' }}</td></tr>
                                        @endif
                                        <tr><td class="text-muted">Contact Number During Leave</td><td>{{ $val('contact_number_during_leave') ?: '—' }}</td></tr>
                                        <tr><td class="text-muted">Leave Address</td><td>{{ $val('leave_address') ?: '—' }}</td></tr>
                                        <tr><td class="text-muted">Travel Destination</td><td>{{ $val('travel_destination') ?: '—' }}</td></tr>
                                        @php
                                            $reviewLeaveTypeCode = optional($leaveTypes->firstWhere('id', (int) $val('leave_type_id')))->code;
                                        @endphp
                                        @if(in_array($reviewLeaveTypeCode, ['LC5', 'LC10']))
                                        <tr><td class="text-muted">Benefit to University</td><td style="white-space: pre-line;">{{ $val('benefit_to_university') ?: '—' }}</td></tr>
                                        <tr><td class="text-muted">Cost Coverage</td><td>{{ $val('cost_coverage') ? str_replace('_', ' ', ucfirst($val('cost_coverage'))) : '—' }}</td></tr>
                                        @endif
                                        <tr><td class="text-muted">Reason</td><td style="white-space: pre-line;">{{ $val('reason') ?: '—' }}</td></tr>
                                        <tr><td class="text-muted">Handover Notes</td><td style="white-space: pre-line;">{{ $val('handover_notes') ?: '—' }}</td></tr>
                                        <tr><td class="text-muted">Letter Content</td><td style="white-space: pre-line;">{{ $val('letter_content') ?: '—' }}</td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-file-text text-success me-1"></i> Your Leave Application Letter (on the real letterhead)
                            </div>
                            <div class="border rounded-sm" style="overflow: hidden;">
                                <iframe src="{{ route('leave-requests.draft.preview') }}" style="width: 100%; height: 560px; border: 0;"></iframe>
                            </div>
                        </div>

                        <!-- ============================================ -->
                        <!-- STEP 3: SIGN & SUBMIT -->
                        <!-- ============================================ -->
                        <div id="step-3-sign" style="{{ $currentStep == 3 ? '' : 'display:none;' }}">
                            <x-alert-info class="mb-2">
                                Sign below to confirm the letter shown in Review is yours. Signing is optional, but recommended - your handover notes are already part of that formal letter.
                            </x-alert-info>
                            <x-signature-pad
                                name="signature_data"
                                label="Your Signature"
                                :required="false"
                                helpText="Draw with mouse or touch - this appears on your Leave Application Letter."
                            />
                        </div>
                        @endif

                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('leave-requests.my-requests') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>

                            @if(!$isWizard)
                                <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                    <i class="ti ti-device-floppy me-1"></i> Submit Request
                                </button>
                            @elseif($currentStep == 1)
                                {{-- Always here, but disabled until every required section
                                     above is filled - see refreshFormSections(). The server
                                     still clamps this independently either way (saveDraft()). --}}
                                <button type="submit" id="next_review_button" formaction="{{ route('leave-requests.draft.save') }}" formnovalidate name="current_step" value="2" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;" disabled>
                                    Next: Review <i class="ti ti-arrow-right ms-1"></i>
                                </button>
                            @elseif($currentStep == 2)
                                <button type="submit" formaction="{{ route('leave-requests.draft.save') }}" formnovalidate name="current_step" value="1" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                    <i class="ti ti-arrow-left me-1"></i> Back
                                </button>
                                <button type="submit" formaction="{{ route('leave-requests.draft.save') }}" formnovalidate name="current_step" value="3" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                    Next: Sign <i class="ti ti-arrow-right ms-1"></i>
                                </button>
                            @else
                                <button type="submit" formaction="{{ route('leave-requests.draft.save') }}" formnovalidate name="current_step" value="2" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                    <i class="ti ti-arrow-left me-1"></i> Back
                                </button>
                                <button type="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                    <i class="ti ti-send me-1"></i> Submit Leave Request
                                </button>
                            @endif
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-clock me-1 text-primary"></i> Process Steps</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            @foreach($processSteps as $index => $step)
                                <li>{{ $index + 1 }}. {{ $step }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Important Notes</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• The colleague you select will receive a notification to accept or decline</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Medical certificates required for Sick Leave</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Official travel requires benefit to university statement</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• All documents become part of your leave record</small>
                            </div>

</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Calculate days between dates (every calendar day counts - weekends and
// public holidays are NOT excluded, matching the server-side calculation)
function calculateDays() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);

        if (end >= start) {
            const msPerDay = 24 * 60 * 60 * 1000;
            const halfDay = document.getElementById('half_day');
            const isHalfDay = halfDay && !halfDay.disabled && halfDay.value !== 'full';
            const days = isHalfDay ? 0.5 : Math.round((end - start) / msPerDay) + 1;
            document.getElementById('days_count').innerText = days;
        } else {
            document.getElementById('days_count').innerText = '0';
        }
    }
}

// Auto-fill emergency contact details when selected from dropdown
document.getElementById('emergency_contact_id')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const name = selected.dataset.name;
    const phone = selected.dataset.phone;

    if (name && phone) {
        document.getElementById('emergency_contact_name').value = name;
        document.getElementById('emergency_contact_phone').value = phone;
    }

    // Setting .value directly doesn't fire 'input', so the reveal chain
    // needs an explicit nudge here (refreshFormSections is a function
    // declaration further down this same script, so it's already hoisted).
    if (typeof refreshFormSections === 'function') {
        refreshFormSections();
    }
});

// Show certificate requirement for sick leave
document.getElementById('leave_type_id')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const requiresCert = selected.dataset.requiresCert === '1';
    const certDiv = document.getElementById('certificate_requirement');

    if (requiresCert) {
        certDiv.classList.remove('d-none');
    } else {
        certDiv.classList.add('d-none');
    }
});

// File upload preview (non-wizard / on-behalf path only)
document.getElementById('supporting_documents')?.addEventListener('change', function(e) {
    const fileList = document.getElementById('fileList');
    fileList.innerHTML = '';

    const maxFiles = 5;
    const maxSize = 5 * 1024 * 1024; // 5MB

    if (this.files.length > maxFiles) {
        alert(`You can only upload up to ${maxFiles} files`);
        this.value = '';
        return;
    }

    for (let i = 0; i < this.files.length; i++) {
        const file = this.files[i];
        if (file.size > maxSize) {
            alert(`File "${file.name}" exceeds 5MB limit`);
            this.value = '';
            return;
        }

        const div = document.createElement('div');
        div.className = 'd-flex align-items-center gap-1 mb-1';
        div.style.fontSize = '0.6rem';
        div.innerHTML = `
            <i class="ti ti-file text-primary"></i>
            <span>${file.name}</span>
            <span class="text-muted">(${(file.size / 1024).toFixed(1)} KB)</span>
        `;
        fileList.appendChild(div);
    }
});

// Draft document upload-now widget (wizard path only) - uploads immediately
// so the file survives a resumed session, then attaches to the real
// request at final submit.
const draftDocumentInput = document.getElementById('draftDocumentInput');
if (draftDocumentInput) {
    const csrfToken = '{{ csrf_token() }}';
    const draftDocumentList = document.getElementById('draftDocumentList');

    draftDocumentInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
            alert(`File "${file.name}" exceeds 5MB limit`);
            this.value = '';
            return;
        }

        const formData = new FormData();
        formData.append('document', file);
        formData.append('_token', csrfToken);

        fetch('{{ route("leave-requests.draft.documents.upload") }}', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' },
        })
            .then(response => response.json())
            .then(doc => {
                const row = document.createElement('div');
                row.className = 'd-flex align-items-center gap-1 mb-1';
                row.style.fontSize = '0.6rem';
                row.dataset.docId = doc.id;
                row.innerHTML = `
                    <i class="ti ti-file text-primary"></i>
                    <span>${doc.name}</span>
                    <span class="text-muted">(${(doc.size / 1024).toFixed(1)} KB)</span>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1 remove-draft-doc" data-id="${doc.id}">Remove</button>
                `;
                draftDocumentList.appendChild(row);
                draftDocumentInput.value = '';
            })
            .catch(() => alert('Upload failed - please try again.'));
    });

    draftDocumentList?.addEventListener('click', function (e) {
        const button = e.target.closest('.remove-draft-doc');
        if (!button) return;

        fetch(`/leave-requests/draft/documents/${button.dataset.id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        })
            .then(response => response.json())
            .then(() => {
                button.closest('[data-doc-id]').remove();
            })
            .catch(() => alert('Could not remove that file - please try again.'));
    });
}

// Leave policy, shown live. These limits come from the applicant's university
// policy (App\Services\LeavePolicy) - the form only steers the choice, the
// server re-checks every one of them on submit.
const policyLimits = @json($policyLimits);

function applyPolicyLimits() {
    const typeEl = document.getElementById('leave_type_id');
    const startEl = document.getElementById('start_date');
    const endEl = document.getElementById('end_date');
    const returnEl = document.getElementById('expected_return_date');
    const say = (id, text) => { document.getElementById(id).textContent = text || ''; };

    // "Official Travel Details" only makes sense for a type that's actually
    // about official travel - asking every Sick or Annual leave applicant
    // for cost coverage was never meaningful.
    const officialTravelTypes = ['LC5', 'LC10'];
    const selectedCode = typeEl.options[typeEl.selectedIndex]?.dataset.code;
    document.getElementById('official_travel_details').style.display = officialTravelTypes.includes(selectedCode) ? '' : 'none';

    const parse = s => new Date(s + 'T00:00:00Z');
    const add = (s, n) => { const d = parse(s); d.setUTCDate(d.getUTCDate() + n); return d.toISOString().slice(0, 10); };
    const plural = n => n + (n === 1 ? ' day' : ' days');
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const pretty = s => { const d = parse(s); return `${String(d.getUTCDate()).padStart(2, '0')} ${months[d.getUTCMonth()]} ${d.getUTCFullYear()}`; };

    const limits = policyLimits.types[typeEl.value];

    if (!limits) {
        startEl.min = policyLimits.today;
        endEl.min = startEl.value || policyLimits.today;
        endEl.removeAttribute('max');
        returnEl.readOnly = false;
        returnEl.classList.remove('bg-light');
        returnEl.removeAttribute('min');
        returnEl.removeAttribute('max');
        ['leave_type_hint', 'start_date_hint', 'end_date_hint'].forEach(id => say(id, ''));
        document.getElementById('half_day_wrap').style.display = 'none';
        document.getElementById('half_day').disabled = true;
        say('return_date_hint', 'When do you plan to resume work?');
        return;
    }

    // Half a day: only where the policy allows it, and only for a single day.
    const halfWrap = document.getElementById('half_day_wrap');
    const halfEl = document.getElementById('half_day');
    const singleDay = startEl.value && startEl.value === endEl.value;
    if (limits.allow_half_day) {
        halfWrap.style.display = '';
        halfEl.disabled = !singleDay;
        if (!singleDay) halfEl.value = 'full';
        say('half_day_hint', singleDay
            ? 'Half a day counts as 0.5 day. Pick morning or afternoon.'
            : 'To take half a day, choose the same start and end date.');
    } else {
        halfWrap.style.display = 'none';
        halfEl.disabled = true;
        halfEl.value = 'full';
    }

    // Notice: the earliest day leave may start.
    const earliest = add(policyLimits.today, limits.min_notice_days);
    startEl.min = earliest;
    say('start_date_hint', limits.min_notice_days > 0
        ? `Apply at least ${plural(limits.min_notice_days)} ahead - earliest start ${pretty(earliest)}.`
        : '');

    // Yearly entitlement and single-request limit: how long this request may be.
    const start = startEl.value;
    const year = parseInt((start || policyLimits.today).slice(0, 4), 10);
    const used = limits.used[year] ?? 0;
    const remaining = limits.yearly_limit === null ? null : Math.max(0, limits.yearly_limit - used);
    const caps = [limits.max_consecutive_days, remaining].filter(v => v !== null && v !== undefined);
    const maxDays = caps.length ? Math.min(...caps) : null;

    const notes = [];
    if (limits.yearly_limit !== null) {
        notes.push(`${plural(limits.yearly_limit)} a year: ${used} used or pending, ${remaining} left.`);
    }
    if (limits.max_consecutive_days !== null) {
        notes.push(`Up to ${plural(limits.max_consecutive_days)} in one request.`);
    }
    say('leave_type_hint', notes.join(' '));

    endEl.min = start || earliest;
    if (start && maxDays !== null) {
        endEl.max = add(start, Math.max(maxDays, 1) - 1);
        say('end_date_hint', maxDays > 0 ? `Latest end date ${pretty(add(start, maxDays - 1))}.` : 'No days left for this leave type.');
    } else {
        endEl.removeAttribute('max');
        say('end_date_hint', '');
    }

    // Return date: either set by the system, or entered within an allowed window.
    const end = endEl.value;
    if (limits.return_date_rule === 'employee') {
        returnEl.readOnly = false;
        returnEl.classList.remove('bg-light');
        if (end) {
            const lo = add(end, 1);
            const hi = add(end, 1 + limits.max_return_gap_days);
            returnEl.min = lo;
            returnEl.max = hi;
            say('return_date_hint', `Between ${pretty(lo)} and ${pretty(hi)}.`);
        } else {
            returnEl.removeAttribute('min');
            returnEl.removeAttribute('max');
            say('return_date_hint', 'Choose the end date first.');
        }
    } else {
        returnEl.readOnly = true;
        returnEl.classList.add('bg-light');
        returnEl.removeAttribute('min');
        returnEl.removeAttribute('max');
        returnEl.value = end ? add(end, 1) : '';
        say('return_date_hint', 'Set by the system: the day after your leave ends.');
    }
}

['leave_type_id', 'start_date', 'end_date'].forEach(id => {
    document.getElementById(id).addEventListener('change', applyPolicyLimits);
});
applyPolicyLimits();

// Progressive reveal: the form opens with only Leave Details showing, and
// each further group only reveals once the one before it is genuinely
// filled - so a long form doesn't overwhelm someone who's only just
// started, but nothing required stays invisible by the time "Next: Review"
// matters. A resumed draft that already has values further down reveals
// everything up to that point immediately, rather than making them retype
// anything. The button itself stays in place the whole time, just disabled
// until the chain completes - the server (saveDraft()) enforces the same
// thing independently, so this is a convenience, not the real gate.
function setSectionVisible(id, visible) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.display = visible ? '' : 'none';
}

function refreshFormSections() {
    const typeEl = document.getElementById('leave_type_id');
    const selectedOption = typeEl.options[typeEl.selectedIndex];
    const requiresCert = selectedOption?.dataset.requiresCert === '1';

    const val = id => (document.getElementById(id)?.value || '').trim();
    // An on-behalf submission has no Emergency Contact card at all - a field
    // that doesn't exist on the page can't block anything, so it's treated
    // as already satisfied rather than as missing.
    const filledOrAbsent = id => { const el = document.getElementById(id); return !el || val(id) !== ''; };

    // Leave Details: acting staff/dean are only required when their select
    // actually exists - some staff have no eligible colleague listed, or
    // aren't a Dean needing a stand-in at all.
    const actingStaffEl = document.getElementById('acting_staff_id');
    const actingDeanEl = document.getElementById('acting_dean_id');
    const leaveDetailsDone = !!(
        typeEl.value
        && val('start_date')
        && val('end_date')
        && (!actingStaffEl || actingStaffEl.value)
        && (!actingDeanEl || actingDeanEl.value)
    );
    setSectionVisible('tier_emergency', leaveDetailsDone);

    const emergencyDone = leaveDetailsDone
        && filledOrAbsent('emergency_contact_name')
        && filledOrAbsent('emergency_contact_phone');
    setSectionVisible('tier_contact', emergencyDone);

    const contactDone = emergencyDone
        && val('contact_number_during_leave')
        && val('leave_address')
        && val('travel_destination');
    setSectionVisible('tier_handover', contactDone);

    const handoverDone = contactDone && val('handover_notes').length >= 5;
    setSectionVisible('tier_reason', handoverDone);

    const reasonDone = handoverDone && val('reason').length >= 10;
    const nextButton = document.getElementById('next_review_button');
    if (nextButton) {
        nextButton.disabled = !reasonDone;
    }

    // Supporting Documents itself: open and no longer optional-looking once
    // the chosen leave type actually needs a certificate; otherwise a
    // closed, clearly-optional link - nobody loses the ability to attach
    // something voluntarily (e.g. a conference invitation), it just isn't
    // pushed on everyone up front.
    const docsDetails = document.getElementById('supporting_documents_details');
    const docsSummary = document.getElementById('supporting_documents_summary');
    if (docsDetails && docsSummary) {
        docsDetails.open = requiresCert;
        docsSummary.innerHTML = requiresCert
            ? '<i class="ti ti-paperclip me-1"></i> Attach your supporting document'
            : '<i class="ti ti-paperclip me-1"></i> Add supporting documents (optional)';
    }
}

[
    'leave_type_id', 'start_date', 'end_date', 'acting_staff_id', 'acting_dean_id',
].forEach(id => document.getElementById(id)?.addEventListener('change', refreshFormSections));
[
    'emergency_contact_name', 'emergency_contact_phone',
    'contact_number_during_leave', 'leave_address', 'travel_destination',
    'handover_notes', 'reason',
].forEach(id => document.getElementById(id)?.addEventListener('input', refreshFormSections));
refreshFormSections();

// The step-1 fields can be out of range while the wizard shows a later step
// (its "Next" buttons skip browser validation). A browser can't focus a
// hidden field to complain about it, so it would just refuse to submit with
// no message at all. Let the server check instead - it sends the applicant
// back to step 1 with the reason.
document.querySelectorAll('#leaveForm button[type="submit"]').forEach(button => {
    button.addEventListener('click', () => {
        const stepOne = document.getElementById('step-1-fill');
        document.getElementById('leaveForm').noValidate = !!(stepOne && stepOne.style.display === 'none' && stepOne.querySelector(':invalid'));
    });
});

document.getElementById('start_date').addEventListener('change', calculateDays);
document.getElementById('end_date').addEventListener('change', calculateDays);
document.getElementById('half_day').addEventListener('change', calculateDays);

// Initial calculation if values exist
if (document.getElementById('start_date').value && document.getElementById('end_date').value) {
    calculateDays();
}
</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {
    // Plain <select> lists every colleague at once - departments like
    // General Administration have 190+ people, so a live-searchable
    // combobox is the difference between usable and not.
    $('#acting_staff_id').select2({
        placeholder: 'Type to search for a colleague...',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#acting_staff_id').closest('form'),
    });

    // Picking a colleague through select2 sets the value and fires 'change'
    // via jQuery's own event system, not a real DOM event - a plain
    // addEventListener('change', ...) on the element never sees it. Hook it
    // here too so refreshFormSections() (progressive reveal) actually runs.
    $('#acting_staff_id').on('change', function () {
        if (typeof refreshFormSections === 'function') {
            refreshFormSections();
        }
    });
});
</script>
@endpush
@endsection
