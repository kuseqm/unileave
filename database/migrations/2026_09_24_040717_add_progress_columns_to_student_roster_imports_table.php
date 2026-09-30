<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Row counts for a chunked upload so the page can show a real
 * "35 of 70" progress bar, and so an interrupted import is visible in the
 * history (processed_rows < total_rows) instead of looking finished.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_roster_imports', function (Blueprint $table) {
            $table->unsignedInteger('total_rows')->nullable()->after('flagged');
            $table->unsignedInteger('processed_rows')->default(0)->after('total_rows');
        });
    }

    public function down(): void
    {
        Schema::table('student_roster_imports', function (Blueprint $table) {
            $table->dropColumn(['total_rows', 'processed_rows']);
        });
    }
};
