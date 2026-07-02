<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffCertification extends Model
{
    protected $table = 'staff_certifications';
    
    protected $fillable = [
        'staff_id', 'certification_name', 'issuing_body', 'certification_number',
        'issue_date', 'expiry_date', 'is_renewable', 'certificate_file'
    ];
    
    protected $casts = [
        'is_renewable' => 'boolean',
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];
    
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    
    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
    
    public function getExpiresInDaysAttribute()
    {
        if (!$this->expiry_date) return null;
        return now()->diffInDays($this->expiry_date, false);
    }
}