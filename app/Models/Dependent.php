<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dependent extends Model
{
    protected $table = 'dependents';

    protected $fillable = [
        'staff_id', 'full_name', 'relationship', 'date_of_birth',
        'phone', 'email', 'is_emergency_contact', 'is_beneficiary',
        'beneficiary_percentage', 'notes', 'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_emergency_contact' => 'boolean',
        'is_beneficiary' => 'boolean',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
