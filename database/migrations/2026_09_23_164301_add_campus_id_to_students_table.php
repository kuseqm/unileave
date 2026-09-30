<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks which physical campus a student actually attended, independent of
 * their Programme's own `school_id` (a Faculty/Programme is one catalog
 * entry offered across every campus - it is not itself campus-specific).
 * Confirmed by the registrar: SJUT runs 4 real campuses (Mazengo Main,
 * Msalato, St Marks Center, Kigoma Ujiji) and the same Programme can be
 * attended at more than one of them, so campus has to live on the student,
 * not be derived through Programme -> School -> Campus.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('campus_id')->nullable()->after('university_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['campus_id']);
            $table->dropColumn('campus_id');
        });
    }
};
