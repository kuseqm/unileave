<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('permission_requests', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('permission_requests', 'permission_type')) {
                $table->enum('permission_type', ['late_arrival', 'early_departure', 'official_duty', 'personal', 'other'])->default('personal')->after('id');
            }
            if (!Schema::hasColumn('permission_requests', 'date')) {
                $table->date('date')->nullable()->after('permission_type');
            }
            if (!Schema::hasColumn('permission_requests', 'start_time')) {
                $table->time('start_time')->nullable()->after('date');
            }
            if (!Schema::hasColumn('permission_requests', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
            if (!Schema::hasColumn('permission_requests', 'hours')) {
                $table->integer('hours')->nullable()->after('end_time');
            }
            if (!Schema::hasColumn('permission_requests', 'reason')) {
                $table->text('reason')->nullable()->after('hours');
            }
            if (!Schema::hasColumn('permission_requests', 'hod_status')) {
                $table->enum('hod_status', ['pending', 'approved', 'rejected'])->default('pending')->after('reason');
            }
            if (!Schema::hasColumn('permission_requests', 'hr_status')) {
                $table->enum('hr_status', ['pending', 'approved', 'rejected'])->default('pending')->after('hod_status');
            }
            if (!Schema::hasColumn('permission_requests', 'hod_comment')) {
                $table->text('hod_comment')->nullable()->after('hr_status');
            }
            if (!Schema::hasColumn('permission_requests', 'hr_comment')) {
                $table->text('hr_comment')->nullable()->after('hod_comment');
            }
            if (!Schema::hasColumn('permission_requests', 'hod_id')) {
                $table->foreignId('hod_id')->nullable()->constrained('staff')->onDelete('set null')->after('staff_id');
            }
            if (!Schema::hasColumn('permission_requests', 'hr_id')) {
                $table->foreignId('hr_id')->nullable()->constrained('staff')->onDelete('set null')->after('hod_id');
            }
            if (!Schema::hasColumn('permission_requests', 'hod_acted_at')) {
                $table->timestamp('hod_acted_at')->nullable()->after('hod_comment');
            }
            if (!Schema::hasColumn('permission_requests', 'hr_acted_at')) {
                $table->timestamp('hr_acted_at')->nullable()->after('hod_acted_at');
            }
            if (!Schema::hasColumn('permission_requests', 'status')) {
                $table->enum('status', ['pending', 'hod_approved', 'hod_rejected', 'hr_approved', 'hr_rejected', 'approved', 'rejected'])->default('pending')->after('hr_acted_at');
            }
        });
    }

    public function down()
    {
        Schema::table('permission_requests', function (Blueprint $table) {
            $table->dropColumn([
                'permission_type', 'date', 'start_time', 'end_time', 'hours',
                'reason', 'hod_status', 'hr_status', 'hod_comment', 'hr_comment',
                'hod_id', 'hr_id', 'hod_acted_at', 'hr_acted_at', 'status'
            ]);
        });
    }
};