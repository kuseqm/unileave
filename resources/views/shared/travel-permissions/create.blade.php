@extends('layouts.master')

@section('title', 'Apply for Travel Permission')

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
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-plane text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Travel Details</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('travel-permissions.store') }}" method="POST" enctype="multipart/form-data" id="travelForm">
                        @csrf
                        
                        <!-- Destination -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Destination <span class="text-danger">*</span></label>
                            <input type="text" name="destination" class="form-control form-control-sm rounded-sm" value="{{ old('destination') }}" placeholder="e.g., Nairobi, Kenya" style="font-size: 0.7rem;" required>
                        </div>
                        
                        <!-- Date Range -->
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">From Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control form-control-sm rounded-sm" value="{{ old('start_date') }}" style="font-size: 0.7rem;" required>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">To Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm rounded-sm" value="{{ old('end_date') }}" style="font-size: 0.7rem;" required>
                            </div>
                        </div>
                        
                        <!-- Days Display -->
                        <div class="mb-1">
                            <div class="alert alert-info py-1 px-2 rounded-sm mb-1" style="font-size: 0.65rem;">
                                <i class="ti ti-info-circle me-1"></i>
                                <strong>Number of days:</strong> <span id="days_count">0</span> day(s)
                            </div>
                        </div>
                        
                        <!-- Travel Reason (Official/Personal) with Conditional Fields -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Reason for Travel <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input type="radio" name="travel_reason" id="reason_official" value="official" class="form-check-input" {{ old('travel_reason') == 'official' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="reason_official" style="font-size: 0.7rem;">Official (University Business)</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="travel_reason" id="reason_personal" value="personal" class="form-check-input" {{ old('travel_reason') == 'personal' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="reason_personal" style="font-size: 0.7rem;">Personal (Private Travel)</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Official Benefit (shown when official selected) -->
                        <div id="official_block" class="mb-1" style="display: none;">
                            <label class="form-label" style="font-size: 0.65rem;">What benefit will the University derive? <span class="text-danger">*</span></label>
                            <textarea name="official_benefit" rows="3" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" placeholder="Explain how the University will benefit from this travel...">{{ old('official_benefit') }}</textarea>
                            <small class="text-muted" style="font-size: 0.55rem;">Attach invitation letter or endorsement below</small>
                        </div>
                        
                        <!-- Personal Description (shown when personal selected) -->
                        <div id="personal_block" class="mb-1" style="display: none;">
                            <label class="form-label" style="font-size: 0.65rem;">Description for Official Records <span class="text-danger">*</span></label>
                            <textarea name="personal_description" rows="3" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" placeholder="Provide a brief description for official records...">{{ old('personal_description') }}</textarea>
                        </div>
                        
                        <!-- Cost Coverage -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Cost of attendance/travel is covered by <span class="text-danger">*</span></label>
                            <select name="cost_coverage" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                <option value="">Select coverage</option>
                                <option value="organizers_full" {{ old('cost_coverage') == 'organizers_full' ? 'selected' : '' }}>Organizers (Full Paid)</option>
                                <option value="organizers_partial" {{ old('cost_coverage') == 'organizers_partial' ? 'selected' : '' }}>Organizers (Partially Paid)</option>
                                <option value="university" {{ old('cost_coverage') == 'university' ? 'selected' : '' }}>St. John's University of Tanzania</option>
                                <option value="private" {{ old('cost_coverage') == 'private' ? 'selected' : '' }}>Private Travel</option>
                            </select>
                        </div>
                        
                        <!-- Acting Staff -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Who will cover your duties? <span class="text-danger">*</span></label>
                            <select name="acting_staff_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                <option value="">Select Colleague</option>
                                @foreach($colleagues as $colleague)
                                <option value="{{ $colleague->id }}" {{ old('acting_staff_id') == $colleague->id ? 'selected' : '' }}>
                                    {{ $colleague->first_name }} {{ $colleague->last_name }} ({{ $colleague->staff_id }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.55rem;">This person will be notified to accept covering your duties</small>
                        </div>
                        
                        <!-- Evidence Attachments -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-file-text text-danger me-1"></i> Supporting Documents
                            </div>
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
                        </div>
                        
                        <!-- Additional Details -->
                        <div class="mt-2">
                            <label class="form-label" style="font-size: 0.65rem;">Additional Details</label>
                            <textarea name="additional_details" rows="2" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" placeholder="Any other relevant information...">{{ old('additional_details') }}</textarea>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('travel-permissions.my-requests') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-device-floppy me-1"></i> Submit Request
                            </button>
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
                    <div class="alert alert-light border mb-0 py-1 px-2 rounded-sm">
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
                        </div>
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
    
    if (officialRadio.checked) {
        officialBlock.style.display = 'block';
        personalBlock.style.display = 'none';
    } else if (personalRadio.checked) {
        officialBlock.style.display = 'none';
        personalBlock.style.display = 'block';
    } else {
        officialBlock.style.display = 'none';
        personalBlock.style.display = 'none';
    }
}

document.getElementById('start_date').addEventListener('change', calculateDays);
document.getElementById('end_date').addEventListener('change', calculateDays);
document.getElementById('reason_official').addEventListener('change', toggleReasonFields);
document.getElementById('reason_personal').addEventListener('change', toggleReasonFields);

// Initial calculation and toggle
calculateDays();
toggleReasonFields();
</script>
@endsection