<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('departments', function (Blueprint $table) {
            // Add unit_head_staff_id column (for non-academic staff)
            if (!Schema::hasColumn('departments', 'unit_head_staff_id')) {
                $table->unsignedBigInteger('unit_head_staff_id')->nullable()->after('hod_staff_id');
                $table->foreign('unit_head_staff_id')
                      ->references('id')
                      ->on('staff')
                      ->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'unit_head_staff_id')) {
                $table->dropForeign(['unit_head_staff_id']);
                $table->dropColumn('unit_head_staff_id');
            }
        });
    }
};