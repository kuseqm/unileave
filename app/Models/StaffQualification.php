<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffQualification extends Model
{
    protected $table = 'staff_qualifications';
    
    protected $fillable = [
        'staff_id', 'qualification_name', 'institution', 'country', 'year_obtained',
        'grade', 'qualification_type', 'is_highest', 'certificate_file',
        'verification_status', 'verified_by', 'verified_at'
    ];
    
    protected $casts = [
        'is_highest' => 'boolean',
        'verified_at' => 'datetime',
    ];
    
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}