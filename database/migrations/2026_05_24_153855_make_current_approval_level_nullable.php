<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't support change() method directly
            // Skip this migration for SQLite
            return;
        }

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->integer('current_approval_level')->nullable()->change();
        });
    }

    public function down()
    {
        if (DB::getDriverName() === 'sqlite') {
            // Skip for SQLite
            return;
        }

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->integer('current_approval_level')->nullable(false)->change();
        });
    }
};
