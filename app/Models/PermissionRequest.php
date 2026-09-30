<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionRequest extends Model
{
    use HasFactory;

    protected $table = 'permission_requests';

    protected $fillable = [
        'staff_id',
        'hod_id',
        'acting_hod_id',
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
        // Both are decimal(5,2) columns - without a cast they return as a
        // raw string like "1.50", which reads like a price.
        'hours' => 'float',
        'duration_hours' => 'float',
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

    /**
     * Department colleague standing in as acting HOD, when the applicant
     * is their own department's HOD and would otherwise have to approve
     * their own request.
     */
    public function actingHod()
    {
        return $this->belongsTo(User::class, 'acting_hod_id');
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

    /**
     * `start_time`/`end_time` are TIME-only columns, so combine them with
     * `date` to get the real scheduled moment. Several views call
     * Carbon::parse($permission->start_datetime) - without this accessor
     * that attribute doesn't exist, so parse() silently falls back to "now".
     */
    public function getStartDatetimeAttribute(): ?Carbon
    {
        if (! $this->date || ! $this->start_time) {
            return null;
        }

        return Carbon::parse($this->date->toDateString().' '.$this->start_time->format('H:i:s'));
    }

    public function getEndDatetimeAttribute(): ?Carbon
    {
        if (! $this->date || ! $this->end_time) {
            return null;
        }

        return Carbon::parse($this->date->toDateString().' '.$this->end_time->format('H:i:s'));
    }

    public function getDurationDisplayAttribute()
    {
        if ($this->duration_hours) {
            return $this->duration_hours.' hour(s)';
        }

        return $this->hours.' hour(s)';
    }

    /**
     * `hours` is a legacy integer column that rounds away fractional hours
     * (e.g. 1.5 -> 2). `duration_hours` (decimal) holds the accurate value
     * store() always saves alongside it, so prefer that where available.
     */
    public function getHoursAttribute($value)
    {
        // A custom accessor reads raw attributes directly, bypassing the
        // 'hours'/'duration_hours' casts above - cast explicitly here too,
        // or this prints "1.50" instead of "1.5" everywhere it's echoed.
        return (float) ($this->attributes['duration_hours'] ?? $value);
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
        return $query->whereHas('staff.department', function ($q) use ($hodId) {
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
}
