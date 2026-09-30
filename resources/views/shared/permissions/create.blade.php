@extends('layouts.master')

@section('title', 'Apply for Permission')

@push('styles')
<style>
/* Same convention as Leave/Travel - each part of the form reads as its own
   card, colored by a thin left accent rather than a heavy filled header. */
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

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'My Permission Requests', 'url' => route('permissions.my-requests')],
        ['name' => 'Apply for Permission', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="APPLY FOR PERMISSION" description="Request short time off (hours)">
        <a href="{{ route('permissions.my-requests') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <!-- Quick Stats -->
    <div class="row g-2 mb-3">
        <x-stats-card value="Same Day" label="Processing Time" subtext="typically approved within 24hrs" color="info" />
        <x-stats-card value="No Balance" label="Leave Balance" subtext="permissions don't affect balance" color="success" />
        <x-stats-card value="HOD → HR" label="Approval Flow" subtext="simple 2-step approval" color="warning" />
        <x-stats-card value="{{ $thisMonthCount }}" label="Requests This Month" subtext="submitted by you" color="primary" />
    </div>
    
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

    <x-alert-danger />
    
    <div class="row g-2">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-clock text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Permission Details</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('permissions.store') }}" method="POST" id="permissionForm">
                        @csrf

                        <!-- ============================================ -->
                        <!-- PERMISSION DETAILS: type, dates, duration -->
                        <!-- ============================================ -->
                        <div class="form-section-card" style="--section-accent: #5B84C4;">
                        <div class="form-section-card__header">
                            <i class="ti ti-clock text-primary me-1"></i> Permission Details
                        </div>
                        <div class="form-section-card__body">

                        <!-- Permission Type -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Permission Type <span class="text-danger">*</span></label>
                            <select name="permission_type" id="permission_type" class="form-select form-select-sm rounded-sm @error('permission_type') is-invalid @enderror" style="font-size: 0.7rem;" required>
                                <option value="">Select Permission Type</option>
                                <option value="late_arrival" {{ old('permission_type') == 'late_arrival' ? 'selected' : '' }}>Late Arrival</option>
                                <option value="early_departure" {{ old('permission_type') == 'early_departure' ? 'selected' : '' }}>Early Departure</option>
                                <option value="short_leave" {{ old('permission_type') == 'short_leave' ? 'selected' : '' }}>Short Leave (hours)</option>
                                <option value="official_duty" {{ old('permission_type') == 'official_duty' ? 'selected' : '' }}>Official Duty</option>
                                <option value="other" {{ old('permission_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('permission_type')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date & Time Range -->
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Start Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control form-control-sm rounded-sm @error('start_datetime') is-invalid @enderror" value="{{ old('start_datetime') }}" style="font-size: 0.7rem;" required>
                                @error('start_datetime')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">End Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control form-control-sm rounded-sm @error('end_datetime') is-invalid @enderror" value="{{ old('end_datetime') }}" style="font-size: 0.7rem;" required>
                                @error('end_datetime')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Duration Display -->
                        <div class="mb-1">
                            <x-alert-info>
    <strong>Duration:</strong> <span id="duration_display">0</span> hour(s)
</x-alert-info>
                        </div>

                        <!-- Official Duty Description (shown when official_duty selected) -->
                        <div id="official_duty_block" class="mb-1" style="display: none;">
                            <label class="form-label" style="font-size: 0.65rem;">Official Duty Description <span class="text-danger">*</span></label>
                            <textarea name="official_duty_description" rows="3" class="form-control form-control-sm rounded-sm @error('official_duty_description') is-invalid @enderror" style="font-size: 0.7rem;" placeholder="Describe the official duty...">{{ old('official_duty_description') }}</textarea>
                            <small class="text-muted" style="font-size: 0.55rem;">Please provide details about the official duty and any supporting documents</small>
                            @error('official_duty_description')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Acting HOD (only shown to an HOD applying for their own permission) -->
                        @if(isset($actingHodOptions) && $actingHodOptions->isNotEmpty())
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Who will act as HOD to review this request? <span class="text-danger">*</span></label>
                            <select name="acting_hod_id" id="acting_hod_id" class="form-select form-select-sm rounded-sm @error('acting_hod_id') is-invalid @enderror" style="font-size: 0.7rem;" required>
                                <option value="">Select Colleague</option>
                                @foreach($actingHodOptions as $colleague)
                                <option value="{{ $colleague->id }}" {{ old('acting_hod_id') == $colleague->id ? 'selected' : '' }}>
                                    {{ $colleague->title ? $colleague->title . ' ' : '' }}{{ $colleague->first_name }} {{ $colleague->last_name }} ({{ $colleague->staff_id }})
                                </option>
                                @endforeach
                            </select>
                            @error('acting_hod_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" style="font-size: 0.6rem;">As HOD, your own request needs someone else to review it - pick a colleague in your department to stand in.</small>
                        </div>
                        @endif

                        </div><!-- /.form-section-card__body -->
                        </div><!-- /Permission Details -->

                        <!-- ============================================ -->
                        <!-- REASON -->
                        <!-- ============================================ -->
                        <div class="form-section-card" style="--section-accent: #212529;">
                        <div class="form-section-card__header">
                            <i class="ti ti-message-2 text-dark me-1"></i> Reason <span class="text-danger">*</span>
                        </div>
                        <div class="form-section-card__body">
                            <textarea name="reason" rows="3" class="form-control form-control-sm rounded-sm @error('reason') is-invalid @enderror" style="font-size: 0.7rem;" required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div><!-- /.form-section-card__body -->
                        </div><!-- /Reason -->

                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('permissions.my-requests') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-device-floppy me-1"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Info Column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">About Permissions</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">What is a Permission?</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">A permission is short time off (hours) for late arrival, early departure, or official duty.</small>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-hierarchy text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Permission Types</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• <strong>Late Arrival</strong> - Arriving after official start time</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• <strong>Early Departure</strong> - Leaving before official end time</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• <strong>Short Leave</strong> - Time off during working hours</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• <strong>Official Duty</strong> - University business outside office</small>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-chart-line text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Approval Process</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">1. Submit request</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">2. Head of Department reviews</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">3. HR Officer final approval</small>
                            </div>
                        
</x-alert-light>
                    </div>
                    
                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-calendar text-danger mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Important Notes</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• Permissions do NOT deduct from leave balance</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Official duty may require supporting documents</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Submit at least 24 hours in advance when possible</small>
                            </div>
                        
</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Calculate duration in hours
function calculateDuration() {
    const startDatetime = document.getElementById('start_datetime').value;
    const endDatetime = document.getElementById('end_datetime').value;
    
    if (startDatetime && endDatetime) {
        const start = new Date(startDatetime);
        const end = new Date(endDatetime);
        
        if (end > start) {
            const diffMs = end - start;
            const diffHours = diffMs / (1000 * 60 * 60);
            document.getElementById('duration_display').innerText = diffHours.toFixed(1);
        } else {
            document.getElementById('duration_display').innerText = '0';
        }
    }
}

// Show/hide official duty block based on permission type
function toggleOfficialDutyBlock() {
    const permissionType = document.getElementById('permission_type').value;
    const officialBlock = document.getElementById('official_duty_block');
    
    if (permissionType === 'official_duty') {
        officialBlock.style.display = 'block';
    } else {
        officialBlock.style.display = 'none';
    }
}

// Event listeners
document.getElementById('start_datetime').addEventListener('change', calculateDuration);
document.getElementById('end_datetime').addEventListener('change', calculateDuration);
document.getElementById('permission_type').addEventListener('change', toggleOfficialDutyBlock);

// Initial calls
calculateDuration();
toggleOfficialDutyBlock();
</script>
@endsection