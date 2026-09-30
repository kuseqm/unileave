<?php

// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

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
        'national_id_encrypted',
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
        'basic_salary_encrypted',
        'tin_number',
        'tin_number_encrypted',
        'nssf_number',
        'nssf_number_encrypted',
        'employment_status',
        'resignation_date',
        'resignation_reason',
        'phone',
        'status',
        'password_change_required',
        'password_changed_at',
        'current_role',
        'last_login_at',
        'last_login_ip',
        'terms_accepted_at',
        'avatar',
        'id_card_valid_until',
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
        'id_card_valid_until' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'confirmation_date' => 'date',
        'resignation_date' => 'date',
        'basic_salary' => 'decimal:2',
        'last_login_at' => 'datetime',
        'terms_accepted_at' => 'datetime',
        'password_change_required' => 'boolean',
        'password_changed_at' => 'datetime',
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
            $name .= $this->title.' ';
        }
        $name .= $this->first_name;
        if ($this->middle_name) {
            $name .= ' '.$this->middle_name;
        }
        $name .= ' '.$this->last_name;

        return $name;
    }

    /**
     * Get age from date of birth
     */
    public function getAgeAttribute()
    {
        if (! $this->date_of_birth) {
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
        if (! $age) {
            return 'Unknown';
        }
        if ($age <= 25) {
            return '18-25';
        }
        if ($age <= 35) {
            return '26-35';
        }
        if ($age <= 45) {
            return '36-45';
        }
        if ($age <= 55) {
            return '46-55';
        }

        return '55+';
    }

    // ============================================
    // ENCRYPTED DATA ACCESSORS
    // ============================================

    /**
     * Get national ID (prefer encrypted version)
     */
    public function getNationalIdAttribute($value)
    {
        // If encrypted version exists, use it; otherwise fall back to original
        return $this->national_id_encrypted ?? $value;
    }

    /**
     * Get TIN number (prefer encrypted version)
     */
    public function getTinNumberAttribute($value)
    {
        return $this->tin_number_encrypted ?? $value;
    }

    /**
     * Get NSSF number (prefer encrypted version)
     */
    public function getNssfNumberAttribute($value)
    {
        return $this->nssf_number_encrypted ?? $value;
    }

    /**
     * Get basic salary (prefer encrypted version)
     */
    public function getBasicSalaryAttribute($value)
    {
        return $this->basic_salary_encrypted ?? $value;
    }

    /**
     * Set national ID (encrypt automatically)
     */
    public function setNationalIdAttribute($value)
    {
        $this->attributes['national_id'] = $value;
        $this->attributes['national_id_encrypted'] = encrypt($value);
    }

    /**
     * Set TIN number (encrypt automatically)
     */
    public function setTinNumberAttribute($value)
    {
        $this->attributes['tin_number'] = $value;
        $this->attributes['tin_number_encrypted'] = encrypt($value);
    }

    /**
     * Set NSSF number (encrypt automatically)
     */
    public function setNssfNumberAttribute($value)
    {
        $this->attributes['nssf_number'] = $value;
        $this->attributes['nssf_number_encrypted'] = encrypt($value);
    }

    /**
     * Set basic salary (encrypt automatically)
     */
    public function setBasicSalaryAttribute($value)
    {
        $this->attributes['basic_salary'] = $value;
        $this->attributes['basic_salary_encrypted'] = encrypt($value);
    }

    /**
     * Get display name (SURNAME, First Middle)
     */
    public function getDisplayNameAttribute()
    {
        $lastName = strtoupper($this->last_name);
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' '.$this->middle_name;
        }

        return $lastName.', '.$name;
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

    /**
     * A super-admin manages every institution, so any opt-in add-on
     * always reads as enabled for them - single-institution staff only
     * see it once their own university has been granted it (see
     * University::hasAddon()).
     */
    public function hasAddon(string $key): bool
    {
        return $this->hasRole('super-admin') || ($this->university && $this->university->hasAddon($key));
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
                // VC sees all pending approvals for their own institute only -
                // "institution-wide" means their institution, not every
                // institute in a multi-tenant deployment.
                return $query->whereHas('staff', function ($q) {
                    $q->where('university_id', $this->university_id);
                });
            case 'internal-auditor':
                // Auditor sees all for compliance (read-only), scoped to
                // their own institute.
                return $query->whereHas('staff', function ($q) {
                    $q->where('university_id', $this->university_id);
                });
            case 'hod':
                return $query->whereHas('staff.department', function ($q) {
                    $q->where('hod_staff_id', $this->id);
                });
            case 'deputy-hod':
                // Deputy HOD can approve when HOD is on leave
                return $query->whereHas('staff.department', function ($q) {
                    $q->where('hod_staff_id', $this->department->hod_staff_id);
                })->where(function ($q) {
                    $q->whereHas('staff.department.hod', function ($sub) {
                        $sub->whereHas('leaveRequests', function ($leave) {
                            $leave->where('status', 'approved')
                                ->where('start_date', '<=', now())
                                ->where('end_date', '>=', now());
                        });
                    });
                });
            case 'dean':
                return $query->whereHas('staff.department.school', function ($q) {
                    $q->where('dean_staff_id', $this->id);
                });
            case 'dept-secretary':
                // Department Secretary can view but not approve
                return $query->whereHas('staff.department', function ($q) {
                    $q->where('id', $this->department_id);
                });
            case 'dvca':
                // staff_category alone has no institute boundary - a DVC at
                // one university must never see academic staff at another.
                return $query->whereHas('staff', function ($q) {
                    $q->where('staff_category', 'academic')
                        ->where('university_id', $this->university_id);
                })->where('current_approval_level', 'dvca');
            case 'dvc-pfa':
                return $query->whereHas('staff', function ($q) {
                    $q->whereIn('staff_category', ['administrative', 'supporting'])
                        ->where('university_id', $this->university_id);
                })->where('current_approval_level', 'dvc_pfa');
            case 'hr-officer':
                return $query->whereIn('current_approval_level', ['hr', 'hr_logistics'])
                    ->whereHas('staff', function ($q) {
                        $q->where('university_id', $this->university_id);
                    });
            case 'admin':
                return $this->hasRole('super-admin') ? $query : $query->whereHas('staff', function ($q) {
                    $q->where('university_id', $this->university_id);
                });
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
                return $this->hasRole('super-admin') || $leaveRequest->staff->university_id == $this->university_id;
            case 'vice-chancellor':
                return $leaveRequest->staff->university_id == $this->university_id;
            case 'hod':
                return $leaveRequest->staff->department_id == $this->department_id;
            case 'deputy-hod':
                // Deputy HOD can only approve if HOD is on leave
                $hod = $leaveRequest->staff->department->hod;
                if (! $hod) {
                    return false;
                }
                $hodOnLeave = LeaveRequest::where('staff_id', $hod->id)
                    ->where('status', 'approved')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->exists();

                return $hodOnLeave && $leaveRequest->staff->department_id == $this->department_id;
            case 'dean':
                return $leaveRequest->staff->department->school_id == $this->department->school_id;
            case 'dvca':
                // staff_category has no institute boundary on its own - a
                // DVC must never approve leave for a different university.
                return $leaveRequest->staff->staff_category == 'academic'
                    && $leaveRequest->staff->university_id == $this->university_id;
            case 'dvc-pfa':
                return in_array($leaveRequest->staff->staff_category, ['administrative', 'supporting'])
                    && $leaveRequest->staff->university_id == $this->university_id;
            case 'hr-officer':
                return $leaveRequest->staff->university_id == $this->university_id;
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
                // A plain admin is scoped to their own institute; only
                // super-admin (cross-institute) sees every university's staff.
                return $user->hasRole('super-admin') ? $query : $query->where('university_id', $user->university_id);
            case 'vice-chancellor':
            case 'internal-auditor':
                // Institution-wide means their own institution, not every
                // institute in a multi-tenant deployment.
                return $query->where('university_id', $user->university_id);
            case 'hr-officer':
                return $query->where('university_id', $user->university_id);
            case 'dean':
                return $query->whereHas('department.school', function ($q) use ($user) {
                    $q->where('dean_staff_id', $user->id);
                });
            case 'hod':
            case 'unit_head':
            case 'deputy-hod':
            case 'dept-secretary':
                return $query->where('department_id', $user->department_id);
            case 'dvca':
                // staff_category alone has no institute boundary - a DVC at
                // one university must never see academic staff at another.
                return $query->where('staff_category', 'academic')->where('university_id', $user->university_id);
            case 'dvc-pfa':
                return $query->whereIn('staff_category', ['administrative', 'supporting'])->where('university_id', $user->university_id);
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
                return $user->hasRole('super-admin') || $user->university_id == $targetStaff->university_id;
            case 'vice-chancellor':
            case 'internal-auditor':
                return $user->university_id == $targetStaff->university_id;
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
                return $targetStaff->staff_category == 'academic' && $user->university_id == $targetStaff->university_id;
            case 'dvc-pfa':
                return in_array($targetStaff->staff_category, ['administrative', 'supporting']) && $user->university_id == $targetStaff->university_id;
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
                return $user->hasRole('super-admin') ? $query : $query->where('university_id', $user->university_id);
            case 'vice-chancellor':
                return $query->where('university_id', $user->university_id);
            case 'internal-auditor':
                // Auditor can view but not manage
                return $query->whereRaw('1 = 0');
            case 'hr-officer':
                return $query->where('university_id', $user->university_id);
            case 'dean':
                return $query->whereHas('department.school', function ($q) use ($user) {
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
        if (! $this->department) {
            return false;
        }

        return $this->department->hod_staff_id == $this->id;
    }

    /**
     * Check if this user is the Dean of their school
     */
    public function isDean()
    {
        if (! $this->department || ! $this->department->school) {
            return false;
        }

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
