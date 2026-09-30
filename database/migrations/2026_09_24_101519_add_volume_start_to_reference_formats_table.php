<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A file that is already on its fourth volume when the system starts needs
 * to keep numbering as "VOL IV", not begin again at "VOL I".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reference_formats', function (Blueprint $table) {
            $table->unsignedInteger('volume_start')->default(1)->after('file_capacity');
        });
    }

    public function down(): void
    {
        Schema::table('reference_formats', function (Blueprint $table) {
            $table->dropColumn('volume_start');
        });
    }
};
