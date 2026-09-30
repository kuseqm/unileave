<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id', 'action', 'module', 'record_id',
        'old_values', 'new_values', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The Audit Trail Dashboard view was originally built against
     * UnifiedAuditTrail's field names. Since that table is never actually
     * written to, this model is the real activity log - these accessors let
     * the same view render it without renaming every field in the view.
     */
    public function getEntityTypeAttribute(): string
    {
        $labels = [
            'leave' => 'Leave Request',
            'permission' => 'Permission Request',
            'travel' => 'Travel Permission',
            'acting' => 'Acting Response (Leave)',
            'acting_escalation' => 'Acting Escalation',
            'incoming_letter' => 'Incoming Letter',
            'staff' => 'Staff',
            'import' => 'Import',
            'auth' => 'Authentication',
        ];

        if ($label = $labels[strtolower($this->module)] ?? null) {
            return $label;
        }

        // Catch-all activity logging derives module names from route names
        // (e.g. "admin.campuses", "registry.incoming") - turn those into
        // something readable instead of showing the raw dotted string.
        return ucwords(str_replace(['.', '_', '-'], ' ', $this->module));
    }

    public function getEventAttribute(): string
    {
        // Stat-aggregation queries alias `action AS event`, so on those rows
        // 'action' was never selected - fall back to the aliased column.
        return $this->attributes['action'] ?? $this->attributes['event'] ?? '';
    }

    public function getAuditableIdAttribute()
    {
        return $this->record_id;
    }

    public function getUserRoleAttribute(): ?string
    {
        return $this->user?->getRoleNames()->first();
    }

    public function getEventBadgeAttribute(): string
    {
        $action = $this->attributes['action'] ?? $this->attributes['event'] ?? '';

        $color = match ($action) {
            'create', 'approve', 'assign', 'login' => 'success',
            'reject', 'dismiss', 'logout' => 'danger',
            'update', 'status_update', 'view' => 'secondary',
            'password_change' => 'warning',
            default => 'secondary',
        };

        return '<span class="badge bg-'.$color.' rounded-sm">'.ucfirst(str_replace('_', ' ', $action)).'</span>';
    }

    public static function log($action, $module, $recordId = null, $oldValues = null, $newValues = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
