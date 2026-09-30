<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Mr. DACHI, Hassan" / "Mr. MTALIMA, Antony Bedson" - every real letter
 * that names a specific student prefixes a salutation, so this drives
 * that rather than requiring staff to type it by hand each time.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('gender', 1)->nullable()->after('middle_name');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};
