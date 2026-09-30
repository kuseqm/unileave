<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * When the applicant is their own school's Dean, the Dean review stage
     * would otherwise ask them to approve their own request. This column
     * records which HOD in their school they picked to stand in as acting
     * Dean for that one stage - the DVC/HR stages after it are untouched.
     */
    public function up(): void
    {
        Schema::table('travel_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('acting_dean_id')->nullable()->after('acting_staff_id');
            $table->foreign('acting_dean_id')->references('id')->on('staff')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_permissions', function (Blueprint $table) {
            $table->dropForeign(['acting_dean_id']);
            $table->dropColumn('acting_dean_id');
        });
    }
};
