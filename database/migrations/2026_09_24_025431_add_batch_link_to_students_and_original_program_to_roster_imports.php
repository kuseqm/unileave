<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a wrongly-chosen Programme on a roster batch be corrected for the
 * whole batch in one step (no re-upload): each student remembers which
 * batch last wrote them, and the batch remembers the Programme it was
 * originally uploaded under so the mistake stays visible in the history.
 *
 * Existing batches are linked by matching their Campus + Programme +
 * Graduation Year, earliest batch first - a best-effort backfill, exact
 * for the normal one-batch-per-book case.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('roster_import_id')->nullable()->index()->after('imported_by');
        });

        Schema::table('student_roster_imports', function (Blueprint $table) {
            $table->unsignedBigInteger('original_program_id')->nullable()->after('program_id');
        });

        DB::table('student_roster_imports')
            ->whereNotNull('uploaded_at')
            ->whereNotNull('program_id')
            ->orderBy('id')
            ->get()
            ->each(function ($batch) {
                DB::table('students')
                    ->where('university_id', $batch->university_id)
                    ->where('campus_id', $batch->campus_id)
                    ->where('program_id', $batch->program_id)
                    ->where('graduation_year', $batch->graduation_year)
                    ->whereNull('roster_import_id')
                    ->whereNotNull('imported_by')
                    ->update(['roster_import_id' => $batch->id]);
            });
    }

    public function down(): void
    {
        Schema::table('student_roster_imports', function (Blueprint $table) {
            $table->dropColumn('original_program_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['roster_import_id']);
            $table->dropColumn('roster_import_id');
        });
    }
};
