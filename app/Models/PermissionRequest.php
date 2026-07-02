<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermissionRequest extends Model
{
    use HasFactory;

    protected $table = 'permission_requests';

    protected $fillable = [
        'staff_id',
        'hod_id',
        'hr_id',
        'permission_type',
        'date',
        'start_time',
        'end_time',
        'hours',
        'duration_hours',
        'reason',
        'purpose',
        'hod_comment',
        'hr_comment',
        'hod_status',
        'hr_status',
        'status',
        'hod_acted_at',
        'hr_acted_at',
        'rejection_reason',
        'destination_address',
        'distance_km',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'hod_acted_at' => 'datetime',
        'hr_acted_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function hod()
    {
        return $this->belongsTo(User::class, 'hod_id');
    }

    public function hr()
    {
        return $this->belongsTo(User::class, 'hr_id');
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('d M Y') : null;
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time ? $this->start_time->format('H:i') : null;
    }

    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time ? $this->end_time->format('H:i') : null;
    }

    public function getDurationDisplayAttribute()
    {
        if ($this->duration_hours) {
            return $this->duration_hours . ' hour(s)';
        }
        return $this->hours . ' hour(s)';
    }

    public function getPermissionTypeLabelAttribute()
    {
        $labels = [
            'late_arrival' => 'Late Arrival',
            'early_departure' => 'Early Departure',
            'short_leave' => 'Short Leave',
            'official_duty' => 'Official Duty',
            'personal' => 'Personal',
            'other' => 'Other',
        ];
        return $labels[$this->permission_type] ?? ucfirst($this->permission_type);
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'hod_approved' => 'HOD Approved',
            'hod_rejected' => 'HOD Rejected',
            'hr_approved' => 'HR Approved',
            'hr_rejected' => 'HR Rejected',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'hod_approved' => 'info',
            'hod_rejected' => 'danger',
            'hr_approved' => 'info',
            'hr_rejected' => 'danger',
            'approved' => 'success',
            'rejected' => 'danger',
        ];
        return $colors[$this->status] ?? 'secondary';
    }

    public function getHodStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending HOD Approval',
            'approved' => 'Approved by HOD',
            'rejected' => 'Rejected by HOD',
        ];
        return $labels[$this->hod_status] ?? ucfirst($this->hod_status);
    }

    public function getHrStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending HR Approval',
            'approved' => 'Approved by HR',
            'rejected' => 'Rejected by HR',
        ];
        return $labels[$this->hr_status] ?? ucfirst($this->hr_status);
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function scopeForHOD($query, $hodId)
    {
        return $query->whereHas('staff.department', function($q) use ($hodId) {
            $q->where('hod_staff_id', $hodId);
        })->where('hod_status', 'pending');
    }

    public function scopeForHR($query)
    {
        return $query->where('hod_status', 'approved')->where('hr_status', 'pending');
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    public function isFullyApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function markHodApproved($hodId, $comment = null)
    {
        $this->update([
            'hod_status' => 'approved',
            'hod_approved_by' => $hodId,
            'hod_acted_at' => now(),
            'hod_comment' => $comment,
        ]);
        
        // If both HOD and HR are approved, update main status
        if ($this->hr_status === 'approved') {
            $this->update(['status' => 'approved']);
        } else {
            $this->update(['status' => 'hod_approved']);
        }
    }

    public function markHodRejected($hodId, $reason)
    {
        $this->update([
            'hod_status' => 'rejected',
            'hod_approved_by' => $hodId,
            'hod_acted_at' => now(),
            'rejection_reason' => $reason,
            'status' => 'hod_rejected',
        ]);
    }

    public function markHrApproved($hrId, $comment = null)
    {
        $this->update([
            'hr_status' => 'approved',
            'hr_approved_by' => $hrId,
            'hr_acted_at' => now(),
            'hr_comment' => $comment,
        ]);
        
        // If both HOD and HR are approved, update main status
        if ($this->hod_status === 'approved') {
            $this->update(['status' => 'approved']);
        } else {
            $this->update(['status' => 'hr_approved']);
        }
    }

    public function markHrRejected($hrId, $reason)
    {
        $this->update([
            'hr_status' => 'rejected',
            'hr_approved_by' => $hrId,
            'hr_acted_at' => now(),
            'rejection_reason' => $reason,
            'status' => 'hr_rejected',
        ]);
    }
}