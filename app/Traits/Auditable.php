<?php

namespace App\Traits;

use App\Models\UnifiedAuditTrail;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    /**
     * Boot the auditable trait.
     * Removed automatic event logging to prevent boot loop issues.
     * Instead, manually call log methods where needed.
     */
    public static function bootAuditable()
    {
        // Only log specific events manually to avoid boot issues
        // Do NOT automatically log created, updated, deleted here
        // Instead, call log methods explicitly
    }
    
    /**
     * Log an audit event manually
     */
    public function logAudit($event, $action, $oldValues = null, $newValues = null, $metadata = null)
    {
        // Skip if no authenticated user (like in migrations or seeding)
        if (!auth()->check()) {
            return;
        }
        
        try {
            UnifiedAuditTrail::create([
                'auditable_type' => get_class($this),
                'auditable_id' => $this->id,
                'event' => $event,
                'action' => $action,
                'user_id' => auth()->id(),
                'user_role' => auth()->user()?->getRoleNames()->first(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'metadata' => $metadata,
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't break the application
            \Log::error('Failed to log audit: ' . $e->getMessage());
        }
    }

    /**
     * Get all audits for this model
     */
    public function audits()
    {
        return $this->morphMany(UnifiedAuditTrail::class, 'auditable');
    }
}