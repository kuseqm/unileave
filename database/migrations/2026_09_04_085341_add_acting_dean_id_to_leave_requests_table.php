<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * When the Dean of a school applies for their own leave, the Dean
     * review stage would otherwise ask them to approve their own request
     * (dean_staff_id === the requester). This column records which HOD in
     * their school they picked to stand in as acting Dean for that one
     * stage - the rest of the chain (HR, DVC, HR Logistics) is untouched.
     */
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('acting_dean_id')->nullable()->after('acting_staff_id');
            $table->foreign('acting_dean_id')->references('id')->on('staff')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['acting_dean_id']);
            $table->dropColumn('acting_dean_id');
        });
    }
};
