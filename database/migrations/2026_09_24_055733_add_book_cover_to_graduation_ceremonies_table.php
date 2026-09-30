<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional photo of the physical graduation book for a ceremony, so the
 * person checking a graduate's record can see the book it came from.
 * One image per ceremony (i.e. per graduation year), not per graduate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('graduation_ceremonies', function (Blueprint $table) {
            $table->string('book_cover_path')->nullable()->after('ceremony_date');
        });
    }

    public function down(): void
    {
        Schema::table('graduation_ceremonies', function (Blueprint $table) {
            $table->dropColumn('book_cover_path');
        });
    }
};
