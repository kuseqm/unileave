<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\LetterTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use Auditable, HasFactory, LetterTrait;

    // ============================================
    // APPROVAL LEVEL CONSTANTS
    // ============================================

    const LEVEL_HOD = 1;

    const LEVEL_DEAN = 2;

    const LEVEL_HR = 3;

    const LEVEL_DVC = 4;

    const LEVEL_HR_LOGISTICS = 5;

    protected $table = 'leave_requests';

    protected $fillable = [
        'staff_id',
        'acting_staff_id',
        'is_escalation_holder',
        'acting_dean_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'days',
        'reason',
        'status',
        'acting_status',
        'acting_token',
        'acting_requested_at',
        'acting_responded_at',
        'acting_escalation_dismissed_at',
        'acting_escalation_dismissed_by',
        'acting_escalation_dismissal_reason',
        'current_approval_level',
        'letter_content',
        'rejection_reason',

        // Enhanced fields
        'half_day',
        'expected_return_date',
        'emergency_contact_id',
        'emergency_contact_name',
        'emergency_contact_phone',
        'contact_number_during_leave',
        'leave_address',
        'travel_destination',
        'handover_notes',

        // added
        'benefit_to_university',
        'cost_coverage',
        'is_first_time_applicant',
        'approval_history',

        // paid of unpaid leave
        'payment_status',
        'payment_percentage',
        'payment_notes',
        'payment_decision_at',
        'payment_decided_by',

        // travel/subsistence allowance, decided by HR alongside the payment status
        'travel_allowance_granted',
        'travel_allowance_amount',
        'travel_allowance_notes',
        'travel_allowance_decided_at',
        'travel_allowance_decided_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'expected_return_date' => 'date',
        'acting_requested_at' => 'datetime',
        'acting_responded_at' => 'datetime',
        'acting_escalation_dismissed_at' => 'datetime',
        // 'decimal:2' forces a string like "6.00" everywhere the value is
        // printed - reads like a price next to whole-day counts. A plain
        // float still handles genuine fractions (6.5) but prints "6" instead
        // of "6.00" for whole numbers.
        'days' => 'float',
        'approval_history' => 'array', // Cast approval history as array
        'is_escalation_holder' => 'boolean',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Staff who requested the leave
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Staff who will act as hold position (acting staff)
     */
    public function actingStaff()
    {
        return $this->belongsTo(User::class, 'acting_staff_id');
    }

    /**
     * HOD standing in as acting Dean, when the requester is their own
     * school's Dean and would otherwise have to approve their own leave.
     */
    public function actingDean()
    {
        return $this->belongsTo(User::class, 'acting_dean_id');
    }

    /**
     * Who dismissed this from the "Unresponsive Acting Staff" escalation list, if anyone.
     */
    public function dismissedBy()
    {
        return $this->belongsTo(User::class, 'acting_escalation_dismissed_by');
    }

    /**
     * Leave type for this request
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    /**
     * Approval logs for this leave request
     */
    public function approvalLogs()
    {
        return $this->hasMany(ApprovalLog::class, 'request_id')->where('request_type', 'leave');
    }

    /**
     * Emergency contact used for this leave (saved emergency contact)
     */
    public function emergencyContact()
    {
        return $this->belongsTo(StaffEmergencyContact::class, 'emergency_contact_id');
    }

    /**
     * Documents attached to this leave request
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'leave_request_id');
    }

    /**
     * Hold position history for this leave request
     */
    public function holdPositionHistory()
    {
        return $this->hasOne(HoldPositionHistory::class, 'leave_request_id');
    }

    // ============================================
    // LETTER RELATIONSHIPS (NEW)
    // ============================================

    /**
     * All letters generated for this leave request
     */
    public function letters()
    {
        return $this->hasMany(LeaveLetter::class, 'leave_request_id');
    }

    /**
     * Get the latest approval letter
     */
    public function getLatestLetterAttribute()
    {
        return $this->letters()->where('type', 'approval')->latest()->first();
    }

    /**
     * Get all approval letters
     */
    public function getApprovalLettersAttribute()
    {
        return $this->letters()->where('type', 'approval')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get recall letters (if recalled)
     */
    public function getRecallLettersAttribute()
    {
        return $this->letters()->where('type', 'recall')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get rejection letter if rejected
     */
    public function getRejectionLetterAttribute()
    {
        return $this->letters()->where('type', 'rejection')->first();
    }

    /**
     * Get amendment letters
     */
    public function getAmendmentLettersAttribute()
    {
        return $this->letters()->where('type', 'amendment')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Check if this leave has a generated letter
     */
    public function getHasLetterAttribute()
    {
        return $this->letters()->where('type', 'approval')->exists();
    }

    /**
     * Get the primary letter (first approval letter)
     */
    public function getPrimaryLetterAttribute()
    {
        return $this->letters()->where('type', 'approval')->oldest()->first();
    }

    /**
     * Get total number of letters for this request
     */
    public function getTotalLettersCountAttribute()
    {
        return $this->letters()->count();
    }

    /**
     * Get payment or no payment decision
     */
    public function paymentDecidedBy()
    {
        return $this->belongsTo(User::class, 'payment_decided_by');
    }

    public function travelAllowanceDecidedBy()
    {
        return $this->belongsTo(User::class, 'travel_allowance_decided_by');
    }

    // ============================================
    // APPROVAL WORKFLOW METHODS
    // ============================================

    /**
     * Get level name for display
     */
    public function getLevelName($level = null)
    {
        $level = $level ?? $this->current_approval_level;
        $staffCategory = $this->staff ? $this->staff->staff_category : 'academic';

        $names = [
            self::LEVEL_HOD => 'Head of Department',
            self::LEVEL_DEAN => 'Dean of Faculty',
            self::LEVEL_HR => 'Human Resources',
            self::LEVEL_DVC => $staffCategory == 'academic' ? 'DVC Academic' : 'DVC PFA',
            self::LEVEL_HR_LOGISTICS => 'HR Logistics',
        ];

        return $names[$level] ?? 'Unknown';
    }

    /**
     * Check if request is fully approved
     */
    public function isFullyApproved()
    {
        return $this->status == 'approved';
    }

    /**
     * Check if request is pending
     */
    public function isPending()
    {
        return $this->status == 'pending';
    }

    /**
     * Check if request is rejected
     */
    public function isRejected()
    {
        return $this->status == 'rejected';
    }

    // ============================================
    // ACCESSORS
    // ============================================

    /**
     * Get formatted start date
     */
    public function getFormattedStartDateAttribute()
    {
        return $this->start_date ? $this->start_date->format('d M Y') : null;
    }

    /**
     * Get formatted end date
     */
    public function getFormattedEndDateAttribute()
    {
        return $this->end_date ? $this->end_date->format('d M Y') : null;
    }

    /**
     * Get half day display text
     */
    public function getHalfDayDisplayAttribute()
    {
        $options = [
            'full' => 'Full Day',
            'morning' => 'Half Day - Morning',
            'afternoon' => 'Half Day - Afternoon',
        ];

        return $options[$this->half_day] ?? 'Full Day';
    }

    /**
     * Get letter verification URL
     */
    public function getVerificationUrlAttribute()
    {
        if ($this->latestLetter && $this->latestLetter->secure_code) {
            return route('verify.letter', $this->latestLetter->secure_code);
        }

        return null;
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for active acting requests (accepted acting status)
     */
    public function scopeActingAccepted($query)
    {
        return $query->where('acting_status', 'accepted');
    }

    /**
     * Requests still waiting on the designated acting staff to respond, for
     * longer than $hours. Uses acting_requested_at when set (reassignment resets
     * the clock for the new person) and falls back to created_at otherwise.
     *
     * Single source of truth for this calculation — the dashboard widget, sidebar
     * badge, and the "Unresponsive Acting Staff" page all use this scope so their
     * counts can never drift apart the way they previously did.
     */
    public function scopeActingOverdue($query, $hours = 48)
    {
        return $query->where('acting_status', 'pending')
            ->whereRaw('COALESCE(acting_requested_at, created_at) < ?', [now()->subHours($hours)]);
    }

    /**
     * scopeActingOverdue() further narrowed to exclude requests an HOD/HR/admin
     * has dismissed from the escalation list (see acting_escalation_dismissed_at).
     */
    public function scopeActingOverdueVisible($query, $hours = 48)
    {
        return $query->actingOverdue($hours)->whereNull('acting_escalation_dismissed_at');
    }

    /**
     * Scope for requests at specific approval level
     */
    public function scopeAtApprovalLevel($query, $level)
    {
        return $query->where('current_approval_level', $level);
    }

    /**
     * Scope for requests that have letters generated
     */
    public function scopeHasLetters($query)
    {
        return $query->has('letters');
    }

    /**
     * Scope for requests that need action from a specific role
     */
    public function scopeNeedActionFromRole($query, $role)
    {
        // This is a more complex scope that would be implemented in the controller
        return $query;
    }
}
