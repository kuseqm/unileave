<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * When the applicant is their own department's HOD, the HOD review
     * stage would otherwise ask them to approve their own request. This
     * column records which department colleague they picked to stand in
     * as acting HOD for that one stage - the HR stage after it is
     * untouched.
     */
    public function up(): void
    {
        Schema::table('permission_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('acting_hod_id')->nullable()->after('hod_id');
            $table->foreign('acting_hod_id')->references('id')->on('staff')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permission_requests', function (Blueprint $table) {
            $table->dropForeign(['acting_hod_id']);
            $table->dropColumn('acting_hod_id');
        });
    }
};
