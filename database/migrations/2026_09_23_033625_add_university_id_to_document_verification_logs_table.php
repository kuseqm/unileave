<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Denormalized on purpose, set once at INSERT time in each controller
 * (LeaveLetterController/TravelLetterController/VerifyController already
 * have the relevant $staff->university or $log->user->university resolved
 * right there) - the alternative is a different join path per
 * document_type (leave_letters -> leave_requests -> users vs
 * travel_letters -> travel_permissions -> users vs report_logs -> users)
 * on every single read of the admin verification-log page, for a value
 * that never changes after the scan happened. Nullable - a failed lookup
 * against an unknown code (document_type 'unknown') has no institution to
 * attach to.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_verification_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('university_id')->nullable()->after('document_id');
            $table->index('university_id');
        });
    }

    public function down(): void
    {
        Schema::table('document_verification_logs', function (Blueprint $table) {
            $table->dropColumn('university_id');
        });
    }
};
