<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets an admin set a scan record aside without ever deleting it - the log
 * stays queryable for the full lifetime of the system, it just stops
 * cluttering the default "active" list view (see
 * VerificationLogController::archive()/unarchive()).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_verification_logs', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('user_agent');
            $table->index('is_archived');
        });
    }

    public function down(): void
    {
        Schema::table('document_verification_logs', function (Blueprint $table) {
            $table->dropIndex(['is_archived']);
            $table->dropColumn('is_archived');
        });
    }
};
