<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Corrected per the registrar's own explanation: a Programme (e.g. "BSc in
 * Information Technology") is owned by a Faculty/School directly, not by
 * one specific Department - a single programme's curriculum routinely
 * draws courses from several departments (e.g. a combination "BA with
 * Education" programme, or an IT programme taking a Maths course from the
 * Maths department). That per-course department ownership belongs on
 * `courses` itself (Phase B - not built yet), not forced one level up onto
 * the programme. `department_id` was the wrong home for this FK.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        DB::statement('
            UPDATE programs
            INNER JOIN departments ON departments.id = programs.department_id
            SET programs.school_id = departments.school_id
        ');

        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
    }
};
