<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;
    
    protected $table = 'departments';
    
    protected $fillable = [
        'tenant_id',
        'campus_id',
        'school_id',
        'name',
        'code',
        'hod_staff_id',
        'unit_head_staff_id',
        'description',
        'status',
    ];
    
    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // ============================================
    // RELATIONSHIPS
    // ============================================
    
    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }
    
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
    
    public function hod()
    {
        return $this->belongsTo(User::class, 'hod_staff_id');
    }
    
    public function unitHead()
    {
        return $this->belongsTo(User::class, 'unit_head_staff_id');
    }

    public function staff()
    {
        return $this->hasMany(User::class, 'department_id');
    }
    
    /**
     * Offices within this department (for Central Registry)
     */
    public function offices()
    {
        return $this->hasMany(Office::class, 'department_id');
    }
    
    /**
     * HOD Office (the main office for this department)
     */
    public function hodOffice()
    {
        return $this->hasOne(Office::class, 'department_id')->where('name', 'like', '%HOD%');
    }
    
    // ============================================
    // SCOPES
    // ============================================
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function scopeByCampus($query, $campusId)
    {
        return $query->where('campus_id', $campusId);
    }
    
    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
    
    // ============================================
    // ACCESSORS & MUTATORS
    // ============================================
    
    /**
     * Get full name (School - Department) with null safety
     */
    public function getFullNameAttribute()
    {
        $schoolName = $this->school ? $this->school->name : 'No School';
        return $schoolName . ' - ' . $this->name;
    }
    
    /**
     * Get department code with name (e.g., "CS - Computer Science")
     */
    public function getCodeNameAttribute()
    {
        return $this->code . ' - ' . $this->name;
    }
    
    /**
     * Get display name for dropdowns
     */
    public function getDisplayNameAttribute()
    {
        $campusName = $this->campus ? $this->campus->name : '';
        $schoolName = $this->school ? $this->school->name : '';
        
        if ($campusName && $schoolName) {
            return $campusName . ' - ' . $schoolName . ' - ' . $this->name;
        } elseif ($schoolName) {
            return $schoolName . ' - ' . $this->name;
        }
        
        return $this->name;
    }
    
    // ============================================
    // HELPERS
    // ============================================
    
    /**
     * Check if department has a HOD assigned
     */
    public function hasHod()
    {
        return !is_null($this->hod_staff_id);
    }
    
    /**
     * Get total staff count in this department
     */
    public function getStaffCountAttribute()
    {
        return $this->staff()->count();
    }
    
    /**
     * Get total offices in this department
     */
    public function getOfficesCountAttribute()
    {
        return $this->offices()->count();
    }
}