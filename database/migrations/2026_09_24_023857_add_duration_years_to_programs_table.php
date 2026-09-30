<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Normal length of a programme in years, so a student's start year can be
 * estimated from their graduation year (and vice versa) when the
 * registration number carries no year. Backfilled with sensible defaults
 * per level - these are starting guesses to be reviewed in the Academic
 * Catalog, not authoritative data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->unsignedTinyInteger('duration_years')->nullable()->after('study_level');
        });

        DB::table('programs')->where('study_level', 'Bachelor')->update(['duration_years' => 3]);
        DB::table('programs')->where('study_level', 'Bachelor')
            ->where(fn ($query) => $query->where('name', 'like', '%Pharmacy%')->orWhere('name', 'like', '%Nursing%'))
            ->update(['duration_years' => 4]);
        DB::table('programs')->where('study_level', 'Masters')->update(['duration_years' => 2]);
        DB::table('programs')->where('study_level', 'Diploma')->update(['duration_years' => 3]);
        DB::table('programs')->where('study_level', 'NTA Level 4')->update(['duration_years' => 1]);
        DB::table('programs')->where('study_level', 'NTA Level 5')->update(['duration_years' => 2]);
        DB::table('programs')->where('study_level', 'NTA Level 6')->update(['duration_years' => 3]);
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('duration_years');
        });
    }
};
