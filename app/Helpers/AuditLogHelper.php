<?php

namespace App\Helpers;

use App\Models\AuditLog;

class AuditLogHelper
{
    public static function log($action, $module, $recordId = null, $oldValues = null, $newValues = null)
    {
        if (!auth()->check()) {
            return;
        }
        
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}