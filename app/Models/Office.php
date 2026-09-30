<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $table = 'offices';

    protected $fillable = [
        'name',
        'short_name',
        'department_id',
        'campus_id',
        'school_id',
        'head_staff_id',
        'email_alias',
        'is_active',
        'receives_letters',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'receives_letters' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Department that this office belongs to (nullable for central offices)
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Campus where this office is located
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    /**
     * School/Faculty that this office belongs to
     */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Staff member who heads this office
     */
    public function headStaff()
    {
        return $this->belongsTo(User::class, 'head_staff_id');
    }

    /**
     * Staff member who created this office record
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Action officers assigned to this office
     */
    public function actionOfficers()
    {
        return $this->hasMany(OfficeActionOfficer::class, 'office_id');
    }

    /**
     * Default action officer for this office (is_default = true)
     */
    public function defaultActionOfficer()
    {
        return $this->hasOne(OfficeActionOfficer::class, 'office_id')->where('is_default', true);
    }

    /**
     * Incoming letters addressed to this office
     */
    public function incomingLetters()
    {
        return $this->hasMany(IncomingLetter::class, 'office_id');
    }

    // ========== SCOPES ==========

    /**
     * Scope: Only offices that receive letters
     */
    public function scopeReceivesLetters($query)
    {
        return $query->where('receives_letters', true);
    }

    /**
     * Scope: Only active offices
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Central offices (no department)
     */
    public function scopeCentral($query)
    {
        return $query->whereNull('department_id');
    }

    /**
     * Scope: Department-based offices
     */
    public function scopeDepartmentBased($query)
    {
        return $query->whereNotNull('department_id');
    }

    // ========== HELPERS ==========

    /**
     * Get formatted name with department (if applicable)
     */
    public function getFullNameAttribute()
    {
        if ($this->department) {
            return $this->name.' ('.$this->department->name.')';
        }

        return $this->name;
    }

    /**
     * Get the default action officer for this office
     */
    public function getDefaultActionOfficer()
    {
        return $this->defaultActionOfficer()->first()?->staff;
    }
}
