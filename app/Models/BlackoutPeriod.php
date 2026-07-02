<?php
// app/Models/BlackoutPeriod.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlackoutPeriod extends Model
{
    use HasFactory;
    
    protected $table = 'blackout_periods';
    
    protected $fillable = [
        'university_id', 'name', 'start_date', 'end_date', 'restriction_type',
        'affected_departments', 'affected_leave_types', 'affected_categories',
        'override_allowed', 'created_by'
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'affected_departments' => 'array',
        'affected_leave_types' => 'array',
        'affected_categories' => 'array',
        'override_allowed' => 'boolean',
    ];
    
    // Relationships
    public function university()
    {
        return $this->belongsTo(University::class);
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }
    
    public function scopeHardRestriction($query)
    {
        return $query->where('restriction_type', 'hard');
    }
    
    // Helper methods
    public function isActive()
    {
        return $this->start_date <= now() && $this->end_date >= now();
    }
    
    public function affectsAllDepartments()
    {
        return $this->affected_departments === 'all' || empty($this->affected_departments);
    }
    
    public function affectsAllLeaveTypes()
    {
        return $this->affected_leave_types === 'all' || empty($this->affected_leave_types);
    }
}