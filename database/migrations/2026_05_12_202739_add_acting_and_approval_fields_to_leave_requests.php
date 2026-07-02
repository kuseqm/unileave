<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Only add these if they don't exist
            if (!Schema::hasColumn('leave_requests', 'acting_staff_id')) {
                $table->unsignedBigInteger('acting_staff_id')->nullable()->after('staff_id');
                $table->foreign('acting_staff_id')->references('id')->on('staff')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('leave_requests', 'acting_status')) {
                $table->enum('acting_status', ['pending', 'accepted', 'declined'])->default('pending')->after('status');
            }
            
            if (!Schema::hasColumn('leave_requests', 'acting_requested_at')) {
                $table->timestamp('acting_requested_at')->nullable()->after('acting_status');
            }
            
            if (!Schema::hasColumn('leave_requests', 'acting_responded_at')) {
                $table->timestamp('acting_responded_at')->nullable()->after('acting_requested_at');
            }
            
            if (!Schema::hasColumn('leave_requests', 'acting_token')) {
                $table->string('acting_token', 100)->nullable()->after('acting_responded_at');
            }
        });
    }

    public function down()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['acting_staff_id']);
            $table->dropColumn([
                'acting_staff_id',
                'acting_status',
                'acting_requested_at',
                'acting_responded_at',
                'acting_token'
            ]);
        });
    }
};