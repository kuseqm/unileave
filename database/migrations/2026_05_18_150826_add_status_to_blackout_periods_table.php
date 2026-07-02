<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('blackout_periods', function (Blueprint $table) {
            if (!Schema::hasColumn('blackout_periods', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active');
            }
        });
    }

    public function down()
    {
        Schema::table('blackout_periods', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};