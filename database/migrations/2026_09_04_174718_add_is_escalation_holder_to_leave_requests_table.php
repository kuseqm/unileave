<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * True when acting_staff_id was set to an escalation contact (because
     * nobody in the requester's own department/pool was free), not to
     * someone the requester actually picked - drives whether the acting-
     * response page offers "assign someone else" instead of a plain
     * accept/decline for that person.
     */
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->boolean('is_escalation_holder')->default(false)->after('acting_staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('is_escalation_holder');
        });
    }
};
