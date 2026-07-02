<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    use HasFactory;
    
    protected $table = 'schools';
    
    protected $fillable = [
        'campus_id',
        'name',
        'code',
        'dean_staff_id',
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
    
    /**
     * Campus that this school belongs to
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }
    
    /**
     * Dean (User) of this school
     */
    public function dean()
    {
        return $this->belongsTo(User::class, 'dean_staff_id');
    }
    
    /**
     * Departments under this school
     */
    public function departments()
    {
        return $this->hasMany(Department::class, 'school_id');
    }
    
    /**
     * Staff members in this school (via departments)
     * HasManyThrough: School -> Department -> User
     */
    public function staff()
    {
        return $this->hasManyThrough(
            User::class,        // Final model
            Department::class,  // Intermediate model
            'school_id',        // Foreign key on departments table
            'department_id',    // Foreign key on users table
            'id',               // Local key on schools table
            'id'                // Local key on departments table
        );
    }
    
    /**
     * Offices in this school (via departments)
     * For Central Registry
     */
    public function offices()
    {
        return $this->hasManyThrough(
            Office::class,      // Final model
            Department::class,  // Intermediate model
            'school_id',        // Foreign key on departments table
            'department_id',    // Foreign key on offices table
            'id',               // Local key on schools table
            'id'                // Local key on departments table
        );
    }
    
    // ============================================
    // SCOPES
    // ============================================
    
    /**
     * Scope: Only active schools
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    /**
     * Scope: Schools by campus
     */
    public function scopeByCampus($query, $campusId)
    {
        return $query->where('campus_id', $campusId);
    }
    
    // ============================================
    // ACCESSORS & MUTATORS
    // ============================================
    
    /**
     * Get full name (Campus - School) with null safety
     */
    public function getFullNameAttribute()
    {
        $campusName = $this->campus ? $this->campus->name : 'No Campus';
        return $campusName . ' - ' . $this->name;
    }
    
    /**
     * Get school code with name (e.g., "SCI - Faculty of Science")
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
        
        if ($campusName) {
            return $campusName . ' - ' . $this->name;
        }
        
        return $this->name;
    }
    
    // ============================================
    // HELPERS
    // ============================================
    
    /**
     * Check if school has a dean assigned
     */
    public function hasDean()
    {
        return !is_null($this->dean_staff_id);
    }
    
    /**
     * Get total departments count in this school
     */
    public function getDepartmentsCountAttribute()
    {
        return $this->departments()->count();
    }
    
    /**
     * Get total staff count in this school
     */
    public function getStaffCountAttribute()
    {
        return $this->staff()->count();
    }
    
    /**
     * Get total offices in this school (for Central Registry)
     */
    public function getOfficesCountAttribute()
    {
        return $this->offices()->count();
    }
}