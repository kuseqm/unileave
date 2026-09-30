<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffEmergencyContact extends Model
{
    protected $table = 'staff_emergency_contacts';

    protected $fillable = [
        'staff_id', 'full_name', 'relationship', 'phone_primary',
        'phone_secondary', 'email', 'address', 'priority',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
