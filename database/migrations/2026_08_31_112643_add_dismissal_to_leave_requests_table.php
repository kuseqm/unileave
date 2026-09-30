<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Soft-delete-style dismissal for the "Unresponsive Acting Staff" escalation
     * list — lets HOD/HR/admin acknowledge a stuck request without reassigning or
     * overriding it. Reversible: clearing these columns restores it to the list.
     */
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->timestamp('acting_escalation_dismissed_at')->nullable()->after('acting_responded_at');
            $table->unsignedBigInteger('acting_escalation_dismissed_by')->nullable()->after('acting_escalation_dismissed_at');
            $table->string('acting_escalation_dismissal_reason', 500)->nullable()->after('acting_escalation_dismissed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn([
                'acting_escalation_dismissed_at',
                'acting_escalation_dismissed_by',
                'acting_escalation_dismissal_reason',
            ]);
        });
    }
};
