<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The actual free-text a staff member wrote when composing the letter
 * (auto-filled facts plus their own purpose paragraphs) - must be frozen
 * at issue time, not regenerated later, or their own writing would be
 * silently lost/replaced the next time the letter is viewed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('graduation_letters', function (Blueprint $table) {
            $table->text('body_content')->nullable()->after('signatory_title');
        });
    }

    public function down(): void
    {
        Schema::table('graduation_letters', function (Blueprint $table) {
            $table->dropColumn('body_content');
        });
    }
};
