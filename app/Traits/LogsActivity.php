<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log an activity to audit log using the static log method from AuditLog model
     */
    protected function logActivity($action, $module, $recordId = null, $oldValues = null, $newValues = null)
    {
        AuditLog::log($action, $module, $recordId, $oldValues, $newValues);
    }
    
    /**
     * Log staff creation (manual or bulk)
     */
    protected function logStaffCreated($staff, $method = 'manual')
    {
        $this->logActivity(
            'create',
            'Staff',
            $staff->id,
            null,
            [
                'staff_id' => $staff->staff_id,
                'name' => $staff->full_name,
                'email' => $staff->email,
                'method' => $method,
            ]
        );
    }
    
    /**
     * Log staff update with old and new values
     */
    protected function logStaffUpdated($staff, $oldValues, $newValues)
    {
        $this->logActivity(
            'update',
            'Staff',
            $staff->id,
            $oldValues,
            $newValues
        );
    }
    
    /**
     * Log staff deletion
     */
    protected function logStaffDeleted($staff)
    {
        $this->logActivity(
            'delete',
            'Staff',
            $staff->id,
            [
                'staff_id' => $staff->staff_id,
                'name' => $staff->full_name,
                'email' => $staff->email,
            ],
            null
        );
    }
    
    /**
     * Log bulk import
     */
    protected function logBulkImport($importHistory, $successCount, $failCount)
    {
        $this->logActivity(
            'bulk_import',
            'Import',
            $importHistory->id,
            null,
            [
                'filename' => $importHistory->original_filename,
                'total_rows' => $importHistory->total_rows,
                'successful_rows' => $successCount,
                'failed_rows' => $failCount,
                'sequence_number' => $importHistory->sequence_number,
            ]
        );
    }
}