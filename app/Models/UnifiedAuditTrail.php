<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnifiedAuditTrail extends Model
{
    use HasFactory;

    protected $table = 'unified_audit_trails';

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'event',
        'action',
        'user_id',
        'user_role',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'metadata',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeForType($query, $type)
    {
        return $query->where('auditable_type', $type);
    }

    public function scopeForEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    // Accessors
    public function getEntityTypeAttribute()
    {
        $types = [
            'App\Models\Message' => 'Message',
            'App\Models\LeaveRequest' => 'Leave Request',
            'App\Models\PermissionRequest' => 'Permission Request',
            'App\Models\TravelPermission' => 'Travel Permission',
            'App\Models\Letter' => 'Letter',
        ];

        return $types[$this->auditable_type] ?? 'Unknown';
    }

    public function getEventBadgeAttribute()
    {
        return match ($this->event) {
            'created' => '<span class="badge bg-success">Created</span>',
            'updated' => '<span class="badge bg-info">Updated</span>',
            'deleted' => '<span class="badge bg-danger">Deleted</span>',
            'restored' => '<span class="badge bg-success">Restored</span>',
            'viewed' => '<span class="badge bg-secondary">Viewed</span>',
            'replied' => '<span class="badge bg-primary">Replied</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            'accessed_via_code' => '<span class="badge bg-warning">Secure Access</span>',
            default => '<span class="badge bg-secondary">'.$this->event.'</span>',
        };
    }
}
