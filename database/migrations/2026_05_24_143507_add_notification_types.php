<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM(
            'acting_request',
            'approval_pending',
            'approval_pending_urgent',
            'approval_granted',
            'approval_complete',
            'approval_rejected',
            'hold_accepted',
            'hold_declined',
            'leave_cancelled',
            'document_uploaded',
            'leave_approved_final',
            'balance_updated',
            'reminder'
        ) NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM(
            'acting_request',
            'approval_pending',
            'approval_granted',
            'approval_rejected',
            'hold_accepted',
            'hold_declined',
            'leave_cancelled',
            'document_uploaded',
            'leave_approved_final',
            'balance_updated',
            'reminder'
        ) NOT NULL");
    }
};
