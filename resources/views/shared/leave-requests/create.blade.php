@extends('layouts.master')

@section('title', 'Apply for Leave')

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
    
    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <!-- Error Message -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
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
                    <form action="{{ route('leave-requests.store') }}" method="POST" id="leaveForm" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Leave Type -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Leave Type <span class="text-danger">*</span></label>
                            <select name="leave_type_id" id="leave_type_id" class="form-select form-select-sm rounded-sm @error('leave_type_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select Leave Type</option>
                                @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }} data-requires-cert="{{ $type->requires_certificate }}">
                                    {{ $type->name }} ({{ $type->code }}) @if($type->requires_certificate) - Requires Certificate @endif
                                </option>
                                @endforeach
                            </select>
                            @error('leave_type_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Half Day Option -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Leave Period</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input type="radio" name="half_day" id="full_day" value="full" class="form-check-input" checked>
                                    <label class="form-check-label" for="full_day" style="font-size: 0.65rem;">Full Day</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="half_day" id="morning_half" value="morning" class="form-check-input">
                                    <label class="form-check-label" for="morning_half" style="font-size: 0.65rem;">Half Day - Morning</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="half_day" id="afternoon_half" value="afternoon" class="form-check-input">
                                    <label class="form-check-label" for="afternoon_half" style="font-size: 0.65rem;">Half Day - Afternoon</label>
                                </div>
                            </div>
                            <small class="text-muted" style="font-size: 0.55rem;">Half day leave counts as 0.5 day</small>
                        </div>
                        
                        <!-- Date Range -->
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control form-control-sm rounded-sm @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                @error('start_date')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm rounded-sm @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                @error('end_date')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Expected Return Date -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Expected Return Date</label>
                            <input type="date" name="expected_return_date" id="expected_return_date" class="form-control form-control-sm rounded-sm" value="{{ old('expected_return_date') }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                            <small class="text-muted" style="font-size: 0.55rem;">When do you plan to resume work?</small>
                        </div>
                        
                        <!-- Days Display -->
                        <div class="mb-1">
                            <div class="alert alert-info py-1 px-2 rounded-sm mb-1" style="font-size: 0.65rem;">
                                <i class="ti ti-info-circle me-1"></i>
                                <strong>Number of days:</strong> <span id="days_count">0</span> day(s)
                                <span id="half_day_note" class="text-warning d-none"> (Half day selected)</span>
                            </div>
                        </div>
                        
                        <!-- Acting Staff (Hold Position) -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Who will hold your position? <span class="text-danger">*</span></label>
                            <select name="acting_staff_id" class="form-select form-select-sm rounded-sm @error('acting_staff_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select Colleague</option>
                                @foreach($colleagues as $colleague)
                                <option value="{{ $colleague->id }}" {{ old('acting_staff_id') == $colleague->id ? 'selected' : '' }}>
                                    {{ $colleague->title ? $colleague->title . ' ' : '' }}{{ $colleague->first_name }} @if($colleague->middle_name){{ $colleague->middle_name }} @endif{{ $colleague->last_name }} ({{ $colleague->staff_id }})
                                </option>
                                @endforeach
                            </select>
                            @error('acting_staff_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" style="font-size: 0.6rem;">This person will be notified to accept or decline holding your position during your absence.</small>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- EMERGENCY CONTACT SECTION -->
                        <!-- ============================================ -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-phone text-warning me-1"></i> Emergency Contact
                            </div>
                            
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
                                    <label class="form-label" style="font-size: 0.65rem;">Emergency Contact Name</label>
                                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control form-control-sm rounded-sm" value="{{ old('emergency_contact_name') }}" style="font-size: 0.7rem;" placeholder="Full name">
                                </div>
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Emergency Contact Phone</label>
                                    <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control form-control-sm rounded-sm" value="{{ old('emergency_contact_phone') }}" style="font-size: 0.7rem;" placeholder="Phone number">
                                </div>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- CONTACT DURING LEAVE & ADDRESS -->
                        <!-- ============================================ -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-map-pin text-info me-1"></i> Contact Information During Leave
                            </div>
                            
                            <div class="row g-1">
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Contact Number (During Leave)</label>
                                    <input type="text" name="contact_number_during_leave" class="form-control form-control-sm rounded-sm" value="{{ old('contact_number_during_leave') }}" style="font-size: 0.7rem;" placeholder="Phone number where you can be reached">
                                    <small class="text-muted" style="font-size: 0.55rem;">Optional but recommended</small>
                                </div>
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Leave Address</label>
                                    <input type="text" name="leave_address" class="form-control form-control-sm rounded-sm" value="{{ old('leave_address') }}" style="font-size: 0.7rem;" placeholder="Where you will be staying">
                                </div>
                            </div>
                            
                            <div class="row g-1">
                                <div class="col-md-12 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Travel Destination</label>
                                    <input type="text" name="travel_destination" class="form-control form-control-sm rounded-sm" value="{{ old('travel_destination') }}" style="font-size: 0.7rem;" placeholder="Where are you traveling to (if applicable)">
                                </div>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- OFFICIAL TRAVEL DETAILS (NEW) -->
                        <!-- ============================================ -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-briefcase text-primary me-1"></i> Official Travel Details
                            </div>
                            
                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Benefit to University</label>
                                <textarea name="benefit_to_university" rows="2" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" placeholder="What benefit will the university derive from this travel? (Required for official travel)">{{ old('benefit_to_university') }}</textarea>
                                <small class="text-muted" style="font-size: 0.55rem;">Explain how this travel benefits SJUT</small>
                            </div>
                            
                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Cost Coverage</label>
                                <select name="cost_coverage" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                    <option value="">Select who covers the cost</option>
                                    <option value="organizers_full" {{ old('cost_coverage') == 'organizers_full' ? 'selected' : '' }}>Organizers (Fully Paid)</option>
                                    <option value="organizers_partial" {{ old('cost_coverage') == 'organizers_partial' ? 'selected' : '' }}>Organizers (Partially Paid)</option>
                                    <option value="sjut" {{ old('cost_coverage') == 'sjut' ? 'selected' : '' }}>SJUT</option>
                                    <option value="private" {{ old('cost_coverage') == 'private' ? 'selected' : '' }}>Private Travel</option>
                                </select>
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" name="is_first_time_applicant" class="form-check-input" id="firstTimeApplicant" value="1" {{ old('is_first_time_applicant') ? 'checked' : '' }}>
                                <label class="form-check-label" for="firstTimeApplicant" style="font-size: 0.65rem;">
                                    This is my first time applying for this type of leave
                                </label>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- HANDOVER NOTES -->
                        <!-- ============================================ -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-file-description text-success me-1"></i> Handover Notes
                            </div>
                            
                            <div class="mb-1">
                                <textarea name="handover_notes" rows="3" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" placeholder="Describe your pending tasks, ongoing projects, and who will handle them during your absence...">{{ old('handover_notes') }}</textarea>
                                <small class="text-muted" style="font-size: 0.55rem;">Provide details about work responsibilities and coverage</small>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- SUPPORTING DOCUMENTS -->
                        <!-- ============================================ -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-file-text text-danger me-1"></i> Supporting Documents
                            </div>
                            
                            <div class="mb-1">
                                <input type="file" name="supporting_documents[]" id="supporting_documents" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="text-muted" style="font-size: 0.55rem;">Upload medical certificates, invitation letters, or other supporting documents (Max 5 files, 5MB each)</small>
                                <div id="certificate_requirement" class="text-danger d-none" style="font-size: 0.55rem;">This leave type requires a medical certificate</div>
                            </div>
                            <div id="fileList" class="mt-1"></div>
                        </div>
                        
                        <!-- Reason -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-message-2 me-1"></i> Reason <span class="text-danger">*</span>
                            </div>
                            <div class="mb-1">
                                <textarea name="reason" rows="3" class="form-control form-control-sm rounded-sm @error('reason') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>{{ old('reason') }}</textarea>
                                @error('reason')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Letter Content (Optional) -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-file-text me-1"></i> Letter Content (Optional)
                            </div>
                            <div class="mb-1">
                                <textarea name="letter_content" rows="5" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" placeholder="Write your leave letter here...">{{ old('letter_content') }}</textarea>
                                <small class="text-muted" style="font-size: 0.55rem;">This will be included in the formal leave letter.</small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('leave-requests.my-requests') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Submit Request
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-clock me-1 text-primary"></i> Process Steps</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>1. Select colleague to hold your position</li>
                            <li>2. Colleague accepts/declines (notification sent)</li>
                            <li>3. Head of Unit reviews (if applicable)</li>
                            <li>4. Head of Department reviews</li>
                            <li>5. Dean reviews (academic staff only)</li>
                            <li>6. DVC (Academic/PFA) reviews</li>
                            <li>7. HR Officer processes final balance</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-light border mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Important Notes</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">• The colleague you select will receive a notification to accept or decline</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Medical certificates required for Sick Leave</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• Official travel requires benefit to university statement</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">• All documents become part of your leave record</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Calculate days between dates (excluding weekends, accounting for half day)
function calculateDays() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const halfDay = document.querySelector('input[name="half_day"]:checked').value;
    
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
            
            // If half day, subtract 0.5
            if (halfDay !== 'full') {
                days = days - 0.5;
                document.getElementById('half_day_note').classList.remove('d-none');
                document.getElementById('days_count').innerText = days;
            } else {
                document.getElementById('half_day_note').classList.add('d-none');
                document.getElementById('days_count').innerText = days;
            }
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

// File upload preview
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

// Recalculate days when half day option changes
document.querySelectorAll('input[name="half_day"]').forEach(radio => {
    radio.addEventListener('change', calculateDays);
});

document.getElementById('start_date').addEventListener('change', calculateDays);
document.getElementById('end_date').addEventListener('change', calculateDays);

// Initial calculation if values exist
if (document.getElementById('start_date').value && document.getElementById('end_date').value) {
    calculateDays();
}
</script>
@endsection