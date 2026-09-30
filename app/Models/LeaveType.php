<?php

// app/Models/LeaveType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $table = 'leave_types';

    protected $fillable = [
        'code', 'name', 'description', 'requires_certificate', 'requires_letter',
        'deduct_balance', 'vc_discretion', 'max_consecutive_days',
        'is_configurable', 'config_fields', 'status', 'sort_order',
    ];

    protected $casts = [
        'requires_certificate' => 'boolean',
        'requires_letter' => 'boolean',
        'deduct_balance' => 'boolean',
        'vc_discretion' => 'boolean',
        'is_configurable' => 'boolean',
        'config_fields' => 'array',
    ];

    // Relationships
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Helper methods
    public function requiresCertificateForDays($days)
    {
        if ($this->code === 'LC2') { // Sick Leave
            return $days > 3;
        }

        return $this->requires_certificate;
    }
}
