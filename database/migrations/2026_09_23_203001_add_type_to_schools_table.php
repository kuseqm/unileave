<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SJUT's own "schools" aren't all literally Schools - the real 8 mix
 * Faculty, School, Centre and Institute (e.g. CICT is a Centre, IDS is an
 * Institute), and Directorate/Unit are real possibilities too (per the
 * user) even though none exist yet. `type` makes that distinction a real,
 * selectable field instead of only living inside the free-text `name`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('type')->nullable()->after('name');
        });

        // Backfill the real existing rows from their own names, best-effort.
        DB::table('schools')->where('name', 'like', 'Centre%')->update(['type' => 'Centre']);
        DB::table('schools')->where('name', 'like', 'Faculty%')->update(['type' => 'Faculty']);
        DB::table('schools')->where('name', 'like', 'Institute%')->update(['type' => 'Institute']);
        DB::table('schools')->where('name', 'like', 'School%')->update(['type' => 'School']);
        DB::table('schools')->where('name', 'like', 'Directorate%')->update(['type' => 'Directorate']);
        DB::table('schools')->where('name', 'like', 'Unit%')->update(['type' => 'Unit']);
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
