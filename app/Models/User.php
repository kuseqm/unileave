<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    
    protected $table = 'staff';
    
    protected $fillable = [
        'staff_id', 
        'title', 
        'university_id', 
        'campus_id', 
        'school_id',
        'department_id',
        'reporting_to',
        'first_name', 
        'middle_name',
        'marital_status',
        'spouse_name',
        'last_name', 
        'gender',
        'date_of_birth',
        'national_id',
        'email', 
        'password', 
        'staff_category',
        'job_title',
        'qualification',
        'joining_date', 
        'contract_type',
        'contract_start_date',
        'contract_end_date',
        'confirmation_date',
        'job_grade',
        'basic_salary',
        'tin_number',
        'nssf_number',
        'employment_status',
        'resignation_date',
        'resignation_reason',
        'phone', 
        'status', 
        'password_change_required',
        'current_role', 
        'last_login_at', 
        'last_login_ip',
        'avatar',
        'benefit_to_university',
        'cost_coverage',
        'is_first_time_applicant',
    ];
    
    protected $hidden = [
        'password', 
        'remember_token',
    ];
    
    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'confirmation_date' => 'date',
        'resignation_date' => 'date',
        'basic_salary' => 'decimal:2',
        'last_login_at' => 'datetime',
        'password_change_required' => 'boolean',
        'is_first_time_applicant' => 'boolean',
    ];
    
    // ============================================
    // ACCESSORS - NAME HANDLING
    // ============================================
    
    /**
     * Get full name with title (Title First Middle Last)
     */
    public function getFullNameAttribute()
    {
        $name = '';
        if ($this->title) {
            $name .= $this->title . ' ';
        }
        $name .= $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        return $name;
    }
    
    /**
     * Get age from date of birth
     */
    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return $this->date_of_birth->age;
    }
    
    /**
     * Get age group for reporting
     */
    public function getAgeGroupAttribute()
    {
        $age = $this->age;
        if (!$age) return 'Unknown';
        if ($age <= 25) return '18-25';
        if ($age <= 35) return '26-35';
        if ($age <= 45) return '36-45';
        if ($age <= 55) return '46-55';
        return '56+';
    }
    
    /**
     * Get display name (SURNAME, First Middle)
     */
    public function getDisplayNameAttribute()
    {
        $lastName = strtoupper($this->last_name);
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        return $lastName . ', ' . $name;
    }
    
    /**
     * Get initials (F M L)
     */
    public function getInitialsAttribute()
    {
        $initials = strtoupper(substr($this->first_name, 0, 1));
        if ($this->middle_name) {
            $initials .= strtoupper(substr($this->middle_name, 0, 1));
        }
        $initials .= strtoupper(substr($this->last_name, 0, 1));
        return $initials;
    }
    
    /**
     * Get surname only (uppercase)
     */
    public function getSurnameAttribute()
    {
        return strtoupper($this->last_name);
    }
    
    // ============================================
    // CONTACT INFORMATION RELATIONSHIPS
    // ============================================
    
    /**
     * Multiple phone numbers for staff
     */
    public function phones()
    {
        return $this->hasMany(StaffPhone::class, 'staff_id');
    }
    
    /**
     * Get primary phone number
     */
    public function getPrimaryPhoneAttribute()
    {
        $primary = $this->phones()->where('is_primary', true)->first();
        return $primary ? $primary->phone_number : $this->phone;
    }
    
    /**
     * Multiple email addresses for staff
     */
    public function emails()
    {
        return $this->hasMany(StaffEmail::class, 'staff_id');
    }
    
    /**
     * Get primary email address
     */
    public function getPrimaryEmailAttribute()
    {
        $primary = $this->emails()->where('is_primary', true)->first();
        return $primary ? $primary->email : $this->email;
    }
    
    // ============================================
    // PROFESSIONAL & QUALIFICATIONS
    // ============================================
    
    /**
     * Qualifications (degrees, diplomas, certificates)
     */
    public function qualifications()
    {
        return $this->hasMany(StaffQualification::class, 'staff_id');
    }
    
    /**
     * Get highest qualification
     */
    public function getHighestQualificationAttribute()
    {
        return $this->qualifications()->where('is_highest', true)->first();
    }
    
    /**
     * Professional certifications
     */
    public function certifications()
    {
        return $this->hasMany(StaffCertification::class, 'staff_id');
    }
    
    /**
     * Languages spoken
     */
    public function languages()
    {
        return $this->hasMany(StaffLanguage::class, 'staff_id');
    }
    
    /**
     * Skills and talents
     */
    public function skills()
    {
        return $this->hasMany(StaffSkill::class, 'staff_id');
    }
    
    // ============================================
    // RECOGNITIONS & MEMBERSHIPS
    // ============================================
    
    /**
     * Awards and recognitions
     */
    public function awards()
    {
        return $this->hasMany(StaffAward::class, 'staff_id');
    }
    
    /**
     * Professional memberships
     */
    public function memberships()
    {
        return $this->hasMany(StaffMembership::class, 'staff_id');
    }
    
    /**
     * Publications (research, books, articles)
     */
    public function publications()
    {
        return $this->hasMany(StaffPublication::class, 'staff_id');
    }
    
    // ============================================
    // DEVELOPMENT & EXPERIENCE
    // ============================================
    
    /**
     * Training attended
     */
    public function trainings()
    {
        return $this->hasMany(StaffTraining::class, 'staff_id');
    }
    
    /**
     * Work experience history
     */
    public function workExperience()
    {
        return $this->hasMany(StaffWorkExperience::class, 'staff_id');
    }
    
    /**
     * Professional referees
     */
    public function referees()
    {
        return $this->hasMany(StaffReferee::class, 'staff_id');
    }
    
    /**
     * Contract history
     */
    public function contracts()
    {
        return $this->hasMany(StaffContract::class, 'staff_id');
    }
    
    /**
     * Get current active contract
     */
    public function getCurrentContractAttribute()
    {
        return $this->contracts()->where('is_current', true)->first();
    }
    
    // ============================================
    // EXISTING RELATIONSHIPS
    // ============================================
    
    public function university()
    {
        return $this->belongsTo(University::class);
    }
    
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }
    
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    /**
     * Staff who report to this user (subordinates)
     */
    public function subordinates()
    {
        return $this->hasMany(User::class, 'reporting_to');
    }
    
    /**
     * Staff who this user reports to (line manager)
     */
    public function reportsTo()
    {
        return $this->belongsTo(User::class, 'reporting_to');
    }
    
    /**
     * Leave requests submitted by this staff
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'staff_id');
    }
    
    /**
     * Leave requests where this staff is acting (holding position)
     */
    public function actingLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'acting_staff_id');
    }
    
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class, 'staff_id');
    }
    
    public function permissionRequests()
    {
        return $this->hasMany(PermissionRequest::class, 'staff_id');
    }
    
    public function travelPermissions()
    {
        return $this->hasMany(TravelPermission::class, 'staff_id');
    }
    
    public function approvalLogs()
    {
        return $this->hasMany(ApprovalLog::class, 'approver_staff_id');
    }
    
    // ============================================
    // CENTRAL REGISTRY RELATIONSHIPS (ADDED)
    // ============================================
    
    /**
     * Offices where this user is the head
     */
    public function headedOffices()
    {
        return $this->hasMany(Office::class, 'head_staff_id');
    }
    
    /**
     * Office action officer assignments
     */
    public function officeAssignments()
    {
        return $this->hasMany(OfficeActionOfficer::class, 'staff_id');
    }
    
    /**
     * Offices where this user is a default action officer
     */
    public function defaultOffices()
    {
        return $this->belongsToMany(Office::class, 'office_action_officers', 'staff_id', 'office_id')
                    ->where('office_action_officers.is_default', true);
    }
    
    /**
     * Incoming letters assigned to this user
     */
    public function assignedIncomingLetters()
    {
        return $this->hasMany(IncomingLetter::class, 'assigned_to');
    }
    
    /**
     * Outgoing letters drafted by this user
     */
    public function draftedOutgoingLetters()
    {
        return $this->hasMany(OutgoingLetter::class, 'drafted_by');
    }
    
    /**
     * Outgoing letters approved by this user
     */
    public function approvedOutgoingLetters()
    {
        return $this->hasMany(OutgoingLetter::class, 'approved_by');
    }
    
    /**
     * Print queue actions by this user
     */
    public function printedItems()
    {
        return $this->hasMany(PrintQueue::class, 'printed_by');
    }
    
    public function dispatchedItems()
    {
        return $this->hasMany(PrintQueue::class, 'dispatched_by');
    }
    
    // ============================================
    // NEW ROLE-BASED METHODS (UPDATED)
    // ============================================
    
    /**
     * Pending approvals for this user (based on role)
     */
    public function pendingApprovals()
    {
        $role = $this->getRoleNames()->first();
        
        $query = LeaveRequest::where('status', 'pending')
            ->where('acting_status', 'accepted');
        
        switch ($role) {
            case 'vice-chancellor':
                // VC sees all pending approvals institution-wide
                return $query;
            case 'internal-auditor':
                // Auditor sees all for compliance (read-only)
                return $query;
            case 'hod':
                return $query->whereHas('staff.department', function($q) {
                    $q->where('hod_staff_id', $this->id);
                });
            case 'deputy-hod':
                // Deputy HOD can approve when HOD is on leave
                return $query->whereHas('staff.department', function($q) {
                    $q->where('hod_staff_id', $this->department->hod_staff_id);
                })->where(function($q) {
                    $q->whereHas('staff.department.hod', function($sub) {
                        $sub->whereHas('leaveRequests', function($leave) {
                            $leave->where('status', 'approved')
                                 ->where('start_date', '<=', now())
                                 ->where('end_date', '>=', now());
                        });
                    });
                });
            case 'dean':
                return $query->whereHas('staff.department.school', function($q) {
                    $q->where('dean_staff_id', $this->id);
                });
            case 'dept-secretary':
                // Department Secretary can view but not approve
                return $query->whereHas('staff.department', function($q) {
                    $q->where('id', $this->department_id);
                });
            case 'dvca':
                return $query->whereHas('staff', function($q) {
                    $q->where('staff_category', 'academic');
                })->where('current_approval_level', 'dvca');
            case 'dvc-pfa':
                return $query->whereHas('staff', function($q) {
                    $q->whereIn('staff_category', ['administrative', 'supporting']);
                })->where('current_approval_level', 'dvc_pfa');
            case 'hr-officer':
                return $query->whereIn('current_approval_level', ['hr', 'hr_logistics']);
            case 'admin':
                return $query;
            default:
                return collect();
        }
    }
    
    /**
     * Check if user can approve a leave request
     */
    public function canApproveLeave($leaveRequest)
    {
        $role = $this->getRoleNames()->first();
        
        switch ($role) {
            case 'admin':
                return true;
            case 'vice-chancellor':
                return true;
            case 'hod':
                return $leaveRequest->staff->department_id == $this->department_id;
            case 'deputy-hod':
                // Deputy HOD can only approve if HOD is on leave
                $hod = $leaveRequest->staff->department->hod;
                if (!$hod) return false;
                $hodOnLeave = LeaveRequest::where('staff_id', $hod->id)
                    ->where('status', 'approved')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->exists();
                return $hodOnLeave && $leaveRequest->staff->department_id == $this->department_id;
            case 'dean':
                return $leaveRequest->staff->department->school_id == $this->department->school_id;
            case 'dvca':
                return $leaveRequest->staff->staff_category == 'academic';
            case 'dvc-pfa':
                return in_array($leaveRequest->staff->staff_category, ['administrative', 'supporting']);
            case 'hr-officer':
                return true;
            default:
                return false;
        }
    }
    
    // ============================================
    // APPROVAL ROLE ACCESSORS (UPDATED)
    // ============================================
    
    public function getHodAttribute()
    {
        if ($this->department) {
            return $this->department->hod;
        }
        return null;
    }
    
    public function getDeanAttribute()
    {
        if ($this->department && $this->department->school) {
            return $this->department->school->dean;
        }
        return null;
    }
    
    public function getDvcAttribute()
    {
        if ($this->staff_category == 'academic') {
            return User::role('dvca')->first();
        } else {
            return User::role('dvc-pfa')->first();
        }
    }
    
    public function getHrOfficerAttribute()
    {
        return User::role('hr-officer')->first();
    }
    
    public function getViceChancellorAttribute()
    {
        return User::role('vice-chancellor')->first();
    }
    
    public function getInternalAuditorAttribute()
    {
        return User::role('internal-auditor')->first();
    }
    
    // ============================================
    // ADDITIONAL RELATIONSHIPS
    // ============================================
    
    public function actingDvcAssignments()
    {
        return $this->hasMany(ActingDvcAssignment::class, 'staff_id');
    }
    
    public function approvedActingDvcAssignments()
    {
        return $this->hasMany(ActingDvcAssignment::class, 'approved_by');
    }
    
    public function createdBlackoutPeriods()
    {
        return $this->hasMany(BlackoutPeriod::class, 'created_by');
    }
    
    public function adjustedLeaveBalances()
    {
        return $this->hasMany(LeaveBalance::class, 'adjusted_by');
    }
    
    public function hodDepartment()
    {
        return $this->hasOne(Department::class, 'hod_staff_id');
    }
    
    public function unitHeadDepartment()
    {
        return $this->hasOne(Department::class, 'unit_head_staff_id');
    }
    
    public function isUnitHead()
    {
        return $this->unitHeadDepartment()->exists();
    }
    
    public function getUnitHeadDepartment()
    {
        return $this->unitHeadDepartment;
    }
    
    // ============================================
    // SCOPES
    // ============================================
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function scopeAcademic($query)
    {
        return $query->where('staff_category', 'academic');
    }
    
    public function scopeAdministrative($query)
    {
        return $query->where('staff_category', 'administrative');
    }
    
    public function scopeSupporting($query)
    {
        return $query->where('staff_category', 'supporting');
    }
    
    // ============================================
    // PROFILE RELATIONSHIPS
    // ============================================
    
    public function dependents()
    {
        return $this->hasMany(Dependent::class, 'staff_id');
    }

    public function addresses()
    {
        return $this->hasMany(StaffAddress::class, 'staff_id');
    }

    public function bankDetails()
    {
        return $this->hasMany(StaffBankDetail::class, 'staff_id');
    }

    public function emergencyContacts()
    {
        return $this->hasMany(StaffEmergencyContact::class, 'staff_id');
    }
    
    public function getPrimaryAddressAttribute()
    {
        return $this->addresses()->where('is_primary', true)->first();
    }
    
    public function getCurrentAddressAttribute()
    {
        return $this->addresses()->where('address_type', 'current')->first();
    }
    
    // ============================================
    // ROLE-BASED STAFF VISIBILITY SCOPES (UPDATED)
    // ============================================
    
    /**
     * Scope for users who can see staff based on their role
     */
    public function scopeVisibleStaff($query)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        switch ($role) {
            case 'admin':
            case 'vice-chancellor':
            case 'internal-auditor':
                return $query;
            case 'hr-officer':
                return $query->where('university_id', $user->university_id);
            case 'dean':
                return $query->whereHas('department.school', function($q) use ($user) {
                    $q->where('dean_staff_id', $user->id);
                });
            case 'hod':
            case 'unit_head':
            case 'deputy-hod':
            case 'dept-secretary':
                return $query->where('department_id', $user->department_id);
            case 'dvca':
                return $query->where('staff_category', 'academic');
            case 'dvc-pfa':
                return $query->whereIn('staff_category', ['administrative', 'supporting']);
            case 'staff':
                return $query->where('id', $user->id);
            default:
                return $query->where('id', $user->id);
        }
    }
    
    /**
     * Check if current user can view a specific staff member
     */
    public function canViewStaff($targetStaff)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        switch ($role) {
            case 'admin':
            case 'vice-chancellor':
            case 'internal-auditor':
                return true;
            case 'hr-officer':
                return $user->university_id == $targetStaff->university_id;
            case 'dean':
                return $user->department && $user->department->school_id == $targetStaff->department->school_id;
            case 'hod':
            case 'unit_head':
            case 'deputy-hod':
            case 'dept-secretary':
                return $user->department_id == $targetStaff->department_id;
            case 'dvca':
                return $targetStaff->staff_category == 'academic';
            case 'dvc-pfa':
                return in_array($targetStaff->staff_category, ['administrative', 'supporting']);
            case 'staff':
                return $user->id == $targetStaff->id;
            default:
                return false;
        }
    }
    
    /**
     * Get the list of staff that this user can manage (approve leave, etc.)
     */
    public function scopeManageableStaff($query)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        switch ($role) {
            case 'admin':
            case 'vice-chancellor':
                return $query;
            case 'internal-auditor':
                // Auditor can view but not manage
                return $query->whereRaw('1 = 0');
            case 'hr-officer':
                return $query->where('university_id', $user->university_id);
            case 'dean':
                return $query->whereHas('department.school', function($q) use ($user) {
                    $q->where('dean_staff_id', $user->id);
                });
            case 'hod':
            case 'unit_head':
            case 'deputy-hod':
                return $query->where('department_id', $user->department_id);
            case 'dept-secretary':
                // Secretary can view but not manage
                return $query->where('department_id', $user->department_id);
            default:
                return $query->where('id', $user->id);
        }
    }
    
    // ============================================
    // APPROVAL WORKFLOW HELPERS (UPDATED)
    // ============================================
    
    /**
     * Get the staff members that report to this user (for approval workflows)
     */
    public function getDirectReports()
    {
        return User::where('reporting_to', $this->id)->get();
    }
    
    /**
     * Check if this user is the HOD of their department
     */
    public function isHod()
    {
        if (!$this->department) return false;
        return $this->department->hod_staff_id == $this->id;
    }
    
    /**
     * Check if this user is the Dean of their school
     */
    public function isDean()
    {
        if (!$this->department || !$this->department->school) return false;
        return $this->department->school->dean_staff_id == $this->id;
    }
    
    /**
     * Check if this user is a Deputy HOD
     */
    public function isDeputyHod()
    {
        return $this->hasRole('deputy-hod');
    }
    
    /**
     * Check if this user is a Department Secretary
     */
    public function isDeptSecretary()
    {
        return $this->hasRole('dept-secretary');
    }
    
    /**
     * Check if this user is Vice Chancellor
     */
    public function isViceChancellor()
    {
        return $this->hasRole('vice-chancellor');
    }
    
    /**
     * Check if this user is Internal Auditor
     */
    public function isInternalAuditor()
    {
        return $this->hasRole('internal-auditor');
    }
    
    /**
     * Get the approval level name for this user's role
     */
    public function getApprovalLevel()
    {
        $role = $this->getRoleNames()->first();
        
        $levels = [
            'vice-chancellor' => 6,
            'dvca' => 5,
            'dvc-pfa' => 5,
            'dean' => 4,
            'hod' => 3,
            'deputy-hod' => 2,
            'hr-officer' => 3,
        ];
        
        return $levels[$role] ?? null;
    }
    
    /**
     * Get the approval level name as string
     */
    public function getApprovalLevelName()
    {
        $role = $this->getRoleNames()->first();
        
        $names = [
            'vice-chancellor' => 'Vice Chancellor',
            'hod' => 'Head of Department',
            'deputy-hod' => 'Deputy Head of Department',
            'dean' => 'Dean of Faculty',
            'dept-secretary' => 'Department Secretary',
            'hr-officer' => 'Human Resources Officer',
            'dvca' => 'Deputy Vice Chancellor (Academic)',
            'dvc-pfa' => 'Deputy Vice Chancellor (PFA)',
            'internal-auditor' => 'Internal Auditor',
            'admin' => 'System Administrator',
        ];
        
        return $names[$role] ?? 'Staff';
    }
    
    /**
     * Get the role hierarchy for display
     */
    public function getRoleHierarchyAttribute()
    {
        $role = $this->getRoleNames()->first();
        
        $hierarchy = [
            'admin' => 10,
            'vice-chancellor' => 9,
            'internal-auditor' => 8,
            'dvca' => 7,
            'dvc-pfa' => 7,
            'dean' => 6,
            'hod' => 5,
            'deputy-hod' => 4,
            'dept-secretary' => 3,
            'hr-officer' => 5,
            'staff' => 1,
        ];
        
        return $hierarchy[$role] ?? 1;
    }
}