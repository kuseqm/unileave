<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "a distinguished Masters student" vs a Bachelor's student - the real
 * Introduction letter phrases this by level, not by parsing the programme
 * name. A program-level attribute, not per-student.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('study_level')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('study_level');
        });
    }
};
