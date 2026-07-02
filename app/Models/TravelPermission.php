<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelPermission extends Model
{
    use HasFactory;

    protected $table = 'travel_permissions';

    protected $fillable = [
        'staff_id',
        'acting_staff_id',
        'destination',
        'start_date',
        'end_date',
        'days',
        'travel_reason',
        'official_benefit',
        'personal_description',
        'cost_coverage',
        'additional_details',
        'acting_status',
        'acting_responded_at',
        'acting_token',
        'acting_comment',
        'status',
        'current_approval_level',
        'rejection_reason',
        'invitation_letter_path',
        'endorsement_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'acting_responded_at' => 'datetime',
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function actingStaff()
    {
        return $this->belongsTo(User::class, 'acting_staff_id');
    }

    // Accessor for days calculation
    public function getDaysAttribute()
    {
        return $this->start_date->diffInWeekdays($this->end_date) + 1;
    }

    // Scope for pending approvals by role
    public function scopePendingForRole($query, $user)
    {
        $role = $user->getRoleNames()->first();
        
        switch ($role) {
            case 'unit_head':
                return $query->where('current_approval_level', 'unit_head')
                    ->whereHas('staff.department', function($q) use ($user) {
                        $q->where('unit_head_staff_id', $user->id);
                    });
            case 'hod':
                return $query->where('current_approval_level', 'hod')
                    ->whereHas('staff.department', function($q) use ($user) {
                        $q->where('hod_staff_id', $user->id);
                    });
            case 'dean':
                return $query->where('current_approval_level', 'dean')
                    ->whereHas('staff.department.school', function($q) use ($user) {
                        $q->where('dean_staff_id', $user->id);
                    });
            case 'dvca':
                return $query->where('current_approval_level', 'dvc')
                    ->whereHas('staff', function($q) {
                        $q->where('staff_category', 'academic');
                    });
            case 'dvc-pfa':
                return $query->where('current_approval_level', 'dvc')
                    ->whereHas('staff', function($q) {
                        $q->where('staff_category', 'administrative');
                    });
            case 'hr-officer':
            case 'hr-manager':
                return $query->where('current_approval_level', 'hr_final');
            default:
                return $query->whereRaw('1 = 0');
        }
    }
}