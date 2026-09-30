@extends('layouts.master')

@section('title', 'HR Comprehensive Review')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'HR Review', 'url' => '#']
    ]" />
    
    <x-page-header title="HR COMPREHENSIVE REVIEW" description="Review leave request with full staff context">
        <a href="{{ route('leave-requests.pending') }}" class="btn btn-outline-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back to Pending
        </a>
    </x-page-header>
    
    <x-alert-danger />
    
    <form method="POST" action="{{ route('leave-requests.hr-review.process', $leaveRequest->id) }}" id="hrReviewForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Left Column - Staff Dossier -->
            <div class="col-lg-7">
                <!-- Staff Dossier Component -->
                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-user me-1"></i>
                        <span style="font-weight: 600;">Staff Dossier</span>
                    </div>
                    <div class="card-body p-2">
                        <x-staff-dossier :staff="$leaveRequest->staff" />
                    </div>
                </div>
                
                <!-- Department Coverage -->
                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-building me-1"></i>
                        <span style="font-weight: 600;">Department Coverage</span>
                    </div>
                    <div class="card-body p-0">
                        @if($departmentStaffOnLeave->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-2">Staff</th>
                                        <th>Leave Period</th>
                                        <th class="pe-2">Days</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($departmentStaffOnLeave as $staffLeave)
                                    <tr>
                                        <td class="ps-2">{{ $staffLeave->staff->full_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($staffLeave->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($staffLeave->end_date)->format('d M Y') }}</td>
                                        <td class="pe-2">{{ $staffLeave->days }} days</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-2 text-muted" style="font-size: 0.7rem;">No other staff on leave during this period</div>
                        @endif
                    </div>
                </div>
                
                <!-- Approval History -->
                <div class="card rounded-sm">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-history me-1"></i>
                        <span style="font-weight: 600;">Approval History</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($leaveRequest->approvalLogs->sortBy('created_at') as $log)
                            <div class="list-group-item py-1 px-2" style="font-size: 0.65rem;">
                                <div class="d-flex justify-content-between">
                                    <span><strong>{{ ucfirst($log->from_role) }}</strong> → {{ ucfirst($log->to_role) }}</span>
                                    <span class="text-{{ $log->action == 'approved' ? 'success' : 'danger' }}">{{ ucfirst($log->action) }}</span>
                                    <span class="text-muted">{{ $log->created_at->format('d M H:i') }}</span>
                                </div>
                                @if($log->comment)
                                <div class="text-muted mt-0" style="font-size: 0.55rem;">"{{ $log->comment }}"</div>
                                @endif
                            </div>
                            @empty
                            <div class="list-group-item text-center text-muted">No approval history yet</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - HR Actions -->
            <div class="col-lg-5">
                <!-- Current Leave Request -->
                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-file-text me-1"></i>
                        <span style="font-weight: 600;">Current Request</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-muted" style="font-size: 0.6rem;">Leave Type</div>
                                <div class="fw-semibold" style="font-size: 0.75rem;">{{ $leaveRequest->leaveType->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted" style="font-size: 0.6rem;">Requested Days</div>
                                <div class="fw-semibold" style="font-size: 0.75rem;" id="requestedDays">{{ $leaveRequest->days }} days</div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="text-muted" style="font-size: 0.6rem;">Period</div>
                                <div style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}</div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="text-muted" style="font-size: 0.6rem;">Reason</div>
                                <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;">{{ $leaveRequest->reason }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- PAYMENT INFORMATION CARD - NEW ADDITION -->
                <!-- ============================================ -->
                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-currency-dollar text-success me-1" style="font-size: 0.8rem;"></i>
                            <span style="font-weight: 600; font-size: 0.7rem;">💰 Payment Decision</span>
                        </div>
                    </div>
                    <div class="card-body py-2 px-2">
                        <x-alert-info>
    <strong>Policy Note:</strong> By default, leave is PAID. Only change to Partial or Unpaid if:
                            <ul class="mb-0 mt-1 ps-3">
                                <li>Staff has exhausted leave balance</li>
                                <li>Leave type policy requires unpaid status</li>
                                <li>Staff request for unpaid leave</li>
                                <li>Disciplinary or administrative reasons</li>
                            </ul>
</x-alert-info>
                        
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.65rem;">Payment Status *</label>
                                <select name="payment_status" id="payment_status" class="form-select form-select-sm rounded-sm" required>
                                    <option value="paid" {{ old('payment_status', $leaveRequest->payment_status ?? 'paid') == 'paid' ? 'selected' : '' }}>
                                        ✅ Paid - Full Salary
                                    </option>
                                    <option value="partial" {{ old('payment_status', $leaveRequest->payment_status ?? '') == 'partial' ? 'selected' : '' }}>
                                        ⚠️ Partial - (Specify Percentage)
                                    </option>
                                    <option value="unpaid" {{ old('payment_status', $leaveRequest->payment_status ?? '') == 'unpaid' ? 'selected' : '' }}>
                                        ❌ Unpaid - No Salary
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6" id="payment_percentage_block" style="display: {{ old('payment_status', $leaveRequest->payment_status ?? '') == 'partial' ? 'block' : 'none' }};">
                                <label class="form-label" style="font-size: 0.65rem;">Payment Percentage</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="payment_percentage" id="payment_percentage" class="form-control form-control-sm" 
                                           value="{{ old('payment_percentage', $leaveRequest->payment_percentage ?? 100) }}" 
                                           min="0" max="100" step="5">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.55rem;">Only applicable for partial payment</small>
                            </div>
                        </div>
                        
                        <div class="mt-2" id="payment_notes_block">
                            <label class="form-label" style="font-size: 0.65rem;">Payment Notes / Justification</label>
                            <textarea name="payment_notes" rows="2" class="form-control form-control-sm rounded-sm" 
                                      style="font-size: 0.7rem;" placeholder="Explain why this leave is unpaid/partial (required for non-paid leaves)...">{{ old('payment_notes', $leaveRequest->payment_notes) }}</textarea>
                            <small class="text-muted" style="font-size: 0.55rem;">This will be visible to DVC, staff, and audit trail</small>
                        </div>
                        
                        <!-- Warning Message -->
                        <x-alert-warning id="payment_warning" class="mt-2 d-none" style="font-size: 0.65rem;" :session="false">
                            <strong>⚠️ Important:</strong> Changing to <span id="warning_status">unpaid/partial</span> will affect staff salary.
                            Please provide clear justification in the notes above. This decision will be recorded for audit purposes.
                        </x-alert-warning>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- TRAVEL ALLOWANCE CARD -->
                <!-- ============================================ -->
                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-plane-departure text-info me-1" style="font-size: 0.8rem;"></i>
                            <span style="font-weight: 600; font-size: 0.7rem;">✈️ Travel Allowance</span>
                        </div>
                    </div>
                    <div class="card-body py-2 px-2">
                        @if($leaveRequest->travel_destination)
                            <div class="mb-2">
                                <div class="text-muted" style="font-size: 0.6rem;">Travel Destination</div>
                                <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;">{{ $leaveRequest->travel_destination }}</div>
                            </div>
                        @endif

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" name="travel_allowance_granted" id="travel_allowance_granted" value="1"
                                   {{ old('travel_allowance_granted', $leaveRequest->travel_allowance_granted) ? 'checked' : '' }}>
                            <label class="form-check-label" for="travel_allowance_granted" style="font-size: 0.65rem;">
                                Grant a travel/subsistence allowance for this leave (separate from salary)
                            </label>
                        </div>

                        <div id="travel_allowance_amount_block" style="display: {{ old('travel_allowance_granted', $leaveRequest->travel_allowance_granted) ? 'block' : 'none' }};">
                            <label class="form-label" style="font-size: 0.65rem;">Allowance Amount (TZS)</label>
                            <input type="number" name="travel_allowance_amount" id="travel_allowance_amount" class="form-control form-control-sm rounded-sm"
                                   value="{{ old('travel_allowance_amount', $leaveRequest->travel_allowance_amount) }}"
                                   min="0" step="1000">
                            <small class="text-muted" style="font-size: 0.55rem;">Enter the amount at the institution's recommended rate for this destination.</small>

                            <label class="form-label mt-2" style="font-size: 0.65rem;">Allowance Notes</label>
                            <textarea name="travel_allowance_notes" rows="2" class="form-control form-control-sm rounded-sm"
                                      style="font-size: 0.7rem;" placeholder="Rate basis, distance, or any other justification...">{{ old('travel_allowance_notes', $leaveRequest->travel_allowance_notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Leave Balance -->
                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-chart-pie me-1"></i>
                        <span style="font-weight: 600;">Leave Balance</span>
                    </div>
                    <div class="card-body">
                        @php
                            $currentBalance = $leaveBalances->where('leave_type_id', $leaveRequest->leave_type_id)->first();
                            $remainingBalance = $currentBalance ? $currentBalance->balance : 0;
                            $projectedBalance = $remainingBalance - $leaveRequest->days;
                        @endphp
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="text-muted" style="font-size: 0.55rem;">Current</div>
                                <div class="fw-semibold" style="font-size: 0.9rem;">{{ number_format($remainingBalance, 1) }}</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted" style="font-size: 0.55rem;">Requested</div>
                                <div class="fw-semibold" style="font-size: 0.9rem;">{{ $leaveRequest->days }}</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted" style="font-size: 0.55rem;">Projected</div>
                                <div class="fw-semibold" style="font-size: 0.9rem; color: {{ $projectedBalance < 0 ? '#DC2626' : '#10B981' }}">
                                    {{ number_format(max(0, $projectedBalance), 1) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Day Adjustment -->
                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-adjustments me-1"></i>
                        <span style="font-weight: 600;">Adjust Leave Days</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.7rem;">Adjusted Days</label>
                            <input type="number" name="adjusted_days" id="adjustedDays" class="form-control form-control-sm" 
                                   value="{{ $leaveRequest->days }}" step="{{ $allowsHalfDays || fmod((float) $leaveRequest->days, 1.0) !== 0.0 ? '0.5' : '1' }}" min="{{ $allowsHalfDays ? '0.5' : '1' }}" max="365" style="font-size: 0.7rem;">
                            <small class="text-muted">
                                Original: {{ $leaveRequest->days }} days
                                @if($leaveRequest->half_day && $leaveRequest->half_day !== 'full')
                                    ({{ $leaveRequest->half_day_display }})
                                @endif
                            </small>
                            <small class="text-muted d-block">
                                {{ $allowsHalfDays ? 'Part of a day is allowed for this leave type (steps of half a day).' : 'Whole days only - this university does not allow half-day leave for this leave type.' }}
                            </small>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.7rem;">Reason for Adjustment</label>
                            <select name="adjustment_reason" class="form-select form-select-sm" style="font-size: 0.7rem;">
                                <option value="">Select reason...</option>
                                <option value="low_balance">Low leave balance</option>
                                <option value="blackout_period">Blackout period overlap</option>
                                <option value="department_needs">Department operational needs</option>
                                <option value="previous_late_return">Previous late return</option>
                                <option value="performance_issue">Performance concerns</option>
                                <option value="other">Other reason</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Risk Alerts -->
                @if($blackoutPeriod || $projectedBalance < 5)
                <div class="card rounded-sm mb-3 border-danger">
                    <div class="card-header py-1 px-2 bg-danger-subtle">
                        <i class="ti ti-alert-triangle text-danger me-1"></i>
                        <span style="font-weight: 600; color: #DC2626;">Risk Alerts</span>
                    </div>
                    <div class="card-body py-2">
                        @if($blackoutPeriod)
                        <x-alert-danger :session="false">
    <strong>Blackout Period:</strong> "{{ $blackoutPeriod->name }}" ({{ \Carbon\Carbon::parse($blackoutPeriod->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($blackoutPeriod->end_date)->format('d M Y') }})
</x-alert-danger>
                        @endif
                        @if($projectedBalance < 5)
                        <x-alert-warning>
    <strong>Low Balance Alert:</strong> Projected balance ({{ number_format($projectedBalance, 1) }} days) is below threshold (5 days)
</x-alert-warning>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- HR Comments & Conditions -->
                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-message-2 me-1"></i>
                        <span style="font-weight: 600;">HR Comments & Conditions</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.7rem;">HR Comments</label>
                            <textarea name="hr_comment" class="form-control form-control-sm" rows="3" style="font-size: 0.7rem;" placeholder="Add your observations, recommendations, or notes for DVC..."></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.7rem;">Approval Conditions (if any)</label>
                            <textarea name="conditions" class="form-control form-control-sm" rows="2" style="font-size: 0.7rem;" placeholder="Any conditions that must be met for this approval..."></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="card rounded-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">
                                <i class="ti ti-check"></i> Approve & Forward to DVC
                            </button>
                            <button type="submit" name="action" value="request_info" class="btn btn-warning btn-sm">
                                <i class="ti ti-message-circle"></i> Request More Information
                            </button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">
                                <i class="ti ti-x"></i> Reject Request
                            </button>
                            <button type="submit" name="action" value="draft" class="btn btn-secondary btn-sm">
                                <i class="ti ti-file-text"></i> Save as Draft
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Payment Status Toggle
    const paymentStatus = document.getElementById('payment_status');
    const paymentPercentageBlock = document.getElementById('payment_percentage_block');
    const paymentWarning = document.getElementById('payment_warning');
    const warningStatus = document.getElementById('warning_status');
    
    function togglePaymentFields() {
        if (paymentStatus && paymentStatus.value === 'partial') {
            if (paymentPercentageBlock) paymentPercentageBlock.style.display = 'block';
            if (paymentWarning) paymentWarning.classList.remove('d-none');
            if (warningStatus) warningStatus.innerText = 'partial payment';
        } else if (paymentStatus && paymentStatus.value === 'unpaid') {
            if (paymentPercentageBlock) paymentPercentageBlock.style.display = 'none';
            if (paymentWarning) paymentWarning.classList.remove('d-none');
            if (warningStatus) warningStatus.innerText = 'unpaid leave';
        } else {
            if (paymentPercentageBlock) paymentPercentageBlock.style.display = 'none';
            if (paymentWarning) paymentWarning.classList.add('d-none');
        }
    }
    
    if (paymentStatus) {
        paymentStatus.addEventListener('change', togglePaymentFields);
        togglePaymentFields();
    }

    // Travel Allowance Toggle
    const travelAllowanceGranted = document.getElementById('travel_allowance_granted');
    const travelAllowanceAmountBlock = document.getElementById('travel_allowance_amount_block');

    function toggleTravelAllowanceFields() {
        if (travelAllowanceAmountBlock) {
            travelAllowanceAmountBlock.style.display = travelAllowanceGranted && travelAllowanceGranted.checked ? 'block' : 'none';
        }
    }

    if (travelAllowanceGranted) {
        travelAllowanceGranted.addEventListener('change', toggleTravelAllowanceFields);
        toggleTravelAllowanceFields();
    }
    
    // Update projected balance when days are adjusted
    const adjustedDaysInput = document.getElementById('adjustedDays');
    const requestedDaysSpan = document.getElementById('requestedDays');
    const remainingBalance = {{ $remainingBalance ?? 0 }};
    
    adjustedDaysInput?.addEventListener('change', function() {
        const newDays = parseFloat(this.value);
        const projected = remainingBalance - newDays;
        const projectedSpan = document.querySelector('.col-4:last-child .fw-semibold');
        if (projectedSpan) {
            projectedSpan.textContent = projected.toFixed(1);
            projectedSpan.style.color = projected < 0 ? '#DC2626' : '#10B981';
        }
        if (requestedDaysSpan) {
            requestedDaysSpan.textContent = newDays + ' days';
        }
    });
</script>
@endsection