<?php
// app/Models/ActingDvcAssignment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActingDvcAssignment extends Model
{
    use HasFactory;
    
    protected $table = 'acting_dvc_assignments';
    
    protected $fillable = [
        'staff_id', 'role', 'start_date', 'end_date', 'reason', 'approved_by', 'status'
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    
    // Relationships
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }
    
    public function scopeForRole($query, $role)
    {
        return $query->where('role', $role);
    }
    
    // Helper methods
    public function isCurrentlyActive()
    {
        return $this->status === 'active' 
            && $this->start_date <= now() 
            && $this->end_date >= now();
    }
}