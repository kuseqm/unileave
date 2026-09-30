<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('approval_logs', function (Blueprint $table) {
            $table->string('to_role')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('approval_logs', function (Blueprint $table) {
            $table->string('to_role')->nullable(false)->change();
        });
    }
};
