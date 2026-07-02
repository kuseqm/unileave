@extends('layouts.master')

@section('title', 'Apply for Leave')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Leave Requests', 'url' => route('admin.leave-requests.my-requests')],
        ['name' => 'Apply for Leave', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="APPLY FOR LEAVE" description="Submit a new leave request">
        <a href="{{ route('admin.leave-requests.my-requests') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    <div class="row g-2">
        <!-- Main Form -->
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Leave Application</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.leave-requests.store') }}" method="POST" id="leaveForm">
                        @csrf
                        
                        <!-- Leave Type -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Leave Type <span class="text-danger">*</span></label>
                            <select name="leave_type_id" id="leave_type_id" class="form-select form-select-sm rounded-sm @error('leave_type_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select Leave Type</option>
                                @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }} ({{ $type->code }})
                                </option>
                                @endforeach
                            </select>
                            @error('leave_type_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
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
                        
                        <!-- Days Display -->
                        <div class="mb-1">
                            <div class="alert alert-info py-1 px-2 rounded-sm mb-1" style="font-size: 0.65rem;">
                                <i class="ti ti-info-circle me-1"></i>
                                <strong>Number of days:</strong> <span id="days_count">0</span> day(s)
                            </div>
                        </div>
                        
                        <!-- Acting Staff (Hold Position) -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Who will hold your position? <span class="text-danger">*</span></label>
                            <select name="acting_staff_id" class="form-select form-select-sm rounded-sm @error('acting_staff_id') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select Colleague</option>
                                @foreach($colleagues as $colleague)
                                <option value="{{ $colleague->id }}" {{ old('acting_staff_id') == $colleague->id ? 'selected' : '' }}>
                                    {{ $colleague->first_name }} {{ $colleague->last_name }} ({{ $colleague->staff_id }})
                                </option>
                                @endforeach
                            </select>
                            @error('acting_staff_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" style="font-size: 0.6rem;">This person will be notified to accept or decline holding your position during your absence.</small>
                        </div>
                        
                        <!-- Reason -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" rows="3" class="form-control form-control-sm rounded-sm @error('reason') is-invalid @enderror" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Letter Content (Optional) -->
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Letter Content (Optional)</label>
                            <textarea name="letter_content" rows="5" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" placeholder="Write your leave letter here...">{{ old('letter_content') }}</textarea>
                            <small class="text-muted" style="font-size: 0.6rem;">This will be included in the formal leave letter.</small>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.leave-requests.my-requests') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="col-lg-5">
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
                                <strong class="d-block" style="font-size: 0.65rem;">Note</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">The colleague you select will receive an email notification to accept or decline holding your position. The request will only proceed if they accept.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Calculate days between dates (excluding weekends)
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

document.getElementById('start_date').addEventListener('change', calculateDays);
document.getElementById('end_date').addEventListener('change', calculateDays);
</script>
@endsection