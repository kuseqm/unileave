<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffMembership extends Model
{
    protected $table = 'staff_memberships';

    protected $fillable = [
        'staff_id', 'organization_name', 'membership_number', 'membership_type',
        'valid_from', 'valid_to', 'is_active', 'certificate_file',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function getIsExpiredAttribute()
    {
        return $this->valid_to && $this->valid_to->isPast();
    }
}
