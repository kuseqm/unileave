<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffWorkExperience extends Model
{
    protected $table = 'staff_work_experience';

    protected $fillable = [
        'staff_id', 'employer_name', 'job_title', 'start_date', 'end_date',
        'is_current', 'responsibilities', 'reason_for_leaving',
        'supervisor_name', 'supervisor_contact',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function getDurationYearsAttribute()
    {
        $end = $this->end_date ?? now();

        return $this->start_date->diffInYears($end);
    }
}
