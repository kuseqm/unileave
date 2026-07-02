<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't support dropping columns with indexes directly
            // Skip this migration for SQLite as it's handled differently
            return;
        }

        // Drop and recreate the column as integer
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('current_approval_level');
        });
        
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->integer('current_approval_level')->default(1)->after('acting_status');
        });
    }

    public function down()
    {
        if (DB::getDriverName() === 'sqlite') {
            // Skip for SQLite
            return;
        }

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('current_approval_level');
            $table->string('current_approval_level')->nullable();
        });
    }
};