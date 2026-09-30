<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Graduation Roster import only ever knows the calendar year a student
 * graduated (from the physical graduation books) - not necessarily the
 * exact ceremony date, which is looked up from `graduation_ceremonies` by
 * this year and used to fill the existing `graduation_date` /
 * `graduation_ceremony_number` columns when a match exists.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedSmallInteger('graduation_year')->nullable()->after('academic_year');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('graduation_year');
        });
    }
};
