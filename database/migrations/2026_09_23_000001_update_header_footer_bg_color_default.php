<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The `universities.header_footer_bg_color` column existed but was never
 * actually wired into the Leave/Travel letter templates - they hardcoded
 * '#e8f4e8' directly instead, which is different from this column's own
 * default ('#f8f9fa'). Now that the letters read this column, every
 * existing row (all still sitting at the untouched schema default - none
 * of them have ever been customized through the admin Letterhead
 * settings page) needs updating first, or wiring it up would silently
 * change every university's rendered letter color the moment this ships.
 *
 * Raw SQL rather than Schema::table(...)->change() - this project doesn't
 * have doctrine/dbal installed, which Laravel's column-modification
 * migrations require, and adding a new dependency for one column default
 * isn't warranted here.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE universities MODIFY header_footer_bg_color VARCHAR(255) NOT NULL DEFAULT '#e8f4e8'");

        DB::table('universities')
            ->where('header_footer_bg_color', '#f8f9fa')
            ->update(['header_footer_bg_color' => '#e8f4e8']);
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE universities MODIFY header_footer_bg_color VARCHAR(255) NOT NULL DEFAULT '#f8f9fa'");

        DB::table('universities')
            ->where('header_footer_bg_color', '#e8f4e8')
            ->update(['header_footer_bg_color' => '#f8f9fa']);
    }
};
