<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE permission_requests MODIFY permission_type ENUM('late_arrival', 'early_departure', 'short_leave', 'official_duty', 'personal', 'other') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE permission_requests MODIFY permission_type ENUM('late_arrival', 'early_departure', 'official_duty', 'other') NOT NULL");
    }
};