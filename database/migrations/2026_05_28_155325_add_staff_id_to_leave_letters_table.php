<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leave_letters', function (Blueprint $table) {
            $table->foreignId('staff_id')->after('leave_request_id')->constrained('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('leave_letters', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropColumn('staff_id');
        });
    }
};
