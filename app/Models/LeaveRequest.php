<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LetterTrait;

class LeaveRequest extends Model
{
    use HasFactory, LetterTrait, Auditable;

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
        'leave_type_id',
        'start_date',
        'end_date',
        'days',
        'reason',
        'status',
        'acting_status',
        'acting_token',
        'acting_responded_at',
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

        //added
        'benefit_to_university',
        'cost_coverage',
        'is_first_time_applicant',
        'approval_history',

        //paid of unpaid leave 
        'payment_status',
        'payment_percentage',
        'payment_notes',
        'payment_decision_at',
        'payment_decided_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'expected_return_date' => 'date',
        'acting_responded_at' => 'datetime',
        'days' => 'decimal:2',
        'approval_history' => 'array', // Cast approval history as array
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
     
     
    // ============================================
    // APPROVAL WORKFLOW METHODS
    // ============================================

    /**
     * Get the approval workflow for this leave type
     */
    public function getWorkflow()
    {
        $leaveTypeName = $this->leaveType ? $this->leaveType->name : '';
        $staffCategory = $this->staff ? $this->staff->staff_category : 'academic';
        
        $workflows = [
            'Short Permission' => [self::LEVEL_HOD, self::LEVEL_DEAN],
            'Travel Permission' => [self::LEVEL_HOD, self::LEVEL_DEAN, self::LEVEL_HR, self::LEVEL_DVC, self::LEVEL_HR_LOGISTICS],
            'Annual Leave' => $staffCategory == 'academic' 
                ? [self::LEVEL_HOD, self::LEVEL_DEAN, self::LEVEL_DVC, self::LEVEL_HR]
                : [self::LEVEL_HOD, self::LEVEL_DEAN, self::LEVEL_DVC, self::LEVEL_HR],
            'Sick Leave' => [self::LEVEL_HOD, self::LEVEL_DEAN, self::LEVEL_HR, self::LEVEL_DVC, self::LEVEL_HR_LOGISTICS],
            'Study Leave' => [self::LEVEL_HOD, self::LEVEL_DEAN, self::LEVEL_HR, self::LEVEL_DVC, self::LEVEL_HR_LOGISTICS],
        ];
        
        // Find matching workflow
        foreach ($workflows as $key => $workflow) {
            if (str_contains($leaveTypeName, $key)) {
                return $workflow;
            }
        }
        
        // Default workflow for other leave types
        return [self::LEVEL_HOD, self::LEVEL_DEAN, self::LEVEL_HR];
    }

    /**
     * Get the next approval level
     */
    public function getNextApprovalLevel()
    {
        $workflow = $this->getWorkflow();
        $currentLevel = $this->current_approval_level ?? self::LEVEL_HOD;
        $currentIndex = array_search($currentLevel, $workflow);
        
        if ($currentIndex !== false && isset($workflow[$currentIndex + 1])) {
            return $workflow[$currentIndex + 1];
        }
        
        return null; // No more levels - approval complete
    }

    /**
     * Get the current approver role name
     */
    public function getCurrentApproverRole()
    {
        $level = $this->current_approval_level ?? self::LEVEL_HOD;
        $staffCategory = $this->staff ? $this->staff->staff_category : 'academic';
        
        $roles = [
            self::LEVEL_HOD => 'hod',
            self::LEVEL_DEAN => 'dean',
            self::LEVEL_HR => 'hr-officer',
            self::LEVEL_DVC => $staffCategory == 'academic' ? 'dvca' : 'dvc-pfa',
            self::LEVEL_HR_LOGISTICS => 'hr-officer',
        ];
        
        return $roles[$level] ?? null;
    }

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
     * Check if user can approve this request at current level
     */
    public function canUserApprove($user)
    {
        $requiredRole = $this->getCurrentApproverRole();
        $userRole = $user->getRoleNames()->first();
        
        // Admin can approve anything
        if ($userRole == 'admin') {
            return true;
        }
        
        // Check role match
        if ($userRole != $requiredRole) {
            return false;
        }
        
        // Additional checks based on role
        switch ($requiredRole) {
            case 'hod':
                return $user->department_id == $this->staff->department_id;
            case 'dean':
                return $user->department && $user->department->school_id == $this->staff->department->school_id;
            case 'hr-officer':
                return true;
            case 'dvca':
                return $this->staff->staff_category == 'academic';
            case 'dvc-pfa':
                return $this->staff->staff_category != 'academic';
            default:
                return false;
        }
    }

    /**
     * Get the current approval level display name
     */
    public function getCurrentApprovalLevelNameAttribute()
    {
        return $this->getLevelName();
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

    /**
     * Get the approval progress percentage
     */
    public function getApprovalProgressAttribute()
    {
        $workflow = $this->getWorkflow();
        $currentLevel = $this->current_approval_level ?? self::LEVEL_HOD;
        $currentIndex = array_search($currentLevel, $workflow);
        $totalLevels = count($workflow);
        
        if ($totalLevels == 0) return 100;
        if ($this->status == 'approved') return 100;
        if ($this->status == 'rejected') return 0;
        
        return round(($currentIndex / $totalLevels) * 100);
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