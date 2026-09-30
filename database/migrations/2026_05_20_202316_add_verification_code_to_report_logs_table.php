<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('report_logs', function (Blueprint $table) {
            $table->string('verification_code', 8)->nullable()->unique()->after('id');
            $table->timestamp('verified_at')->nullable()->after('file_path');
        });
    }

    public function down()
    {
        Schema::table('report_logs', function (Blueprint $table) {
            $table->dropColumn(['verification_code', 'verified_at']);
        });
    }
};
