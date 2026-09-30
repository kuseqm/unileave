<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Every one of these types is already used in a `Notification::create()`
     * call somewhere in the app (Permission, Travel Permission, and the
     * leave-exceeded/unresponsive-acting escalation commands), but was never
     * added to this enum - so every one of those notifications has been
     * silently failing with "Data truncated for column 'type'".
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM(
            'acting_request','approval_pending','approval_pending_urgent','approval_granted',
            'approval_complete','approval_rejected','hold_accepted','hold_declined','leave_cancelled',
            'document_uploaded','leave_approved_final','balance_updated','reminder',
            'letter_pending_approval','letter_pending_signature','letter_approved','letter_signed',
            'letter_rejected','letter_progress_update','letter_completed',
            'acting_reassigned','acting_overridden',
            'permission_pending','permission_hod_approved','permission_pending_hr',
            'permission_approved','permission_rejected',
            'acting_request_travel','acting_accepted_travel','acting_declined_travel',
            'approval_pending_travel','travel_rejected',
            'escalated_unresponsive','leave_exceeded','staff_leave_exceeded','info_requested'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM(
            'acting_request','approval_pending','approval_pending_urgent','approval_granted',
            'approval_complete','approval_rejected','hold_accepted','hold_declined','leave_cancelled',
            'document_uploaded','leave_approved_final','balance_updated','reminder',
            'letter_pending_approval','letter_pending_signature','letter_approved','letter_signed',
            'letter_rejected','letter_progress_update','letter_completed',
            'acting_reassigned','acting_overridden'
        ) NOT NULL");
    }
};
