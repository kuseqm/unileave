<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Two ways to go beyond the fixed wording: a short extra paragraph slotted
 * into a standard letter, and a "custom" letter whose whole body is typed by
 * the person when none of the samples fit.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('verification_letters', function (Blueprint $table) {
            $table->text('extra_text')->nullable()->after('mode_of_study');
            $table->text('body_text')->nullable()->after('extra_text');
        });
    }

    public function down(): void
    {
        Schema::table('verification_letters', function (Blueprint $table) {
            $table->dropColumn(['extra_text', 'body_text']);
        });
    }
};
