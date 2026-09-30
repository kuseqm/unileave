<?php

namespace App\Helpers;

use App\Models\AuditLog;

class AuditLogHelper
{
    public static function log($action, $module, $recordId = null, $oldValues = null, $newValues = null)
    {
        if (! auth()->check()) {
            return;
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            // AuditLog casts these to JSON itself; encoding here too stored a
            // JSON string inside JSON that no one could read back as values.
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
