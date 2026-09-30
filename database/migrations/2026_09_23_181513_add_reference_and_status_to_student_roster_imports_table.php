<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Gives every Graduation Roster batch a trackable reference number (same
 * {role_code}{ymdH}{serial} pattern as the staff-import ImportSequence),
 * created the moment a template is downloaded (not just on upload) so the
 * full lifecycle - downloaded, uploaded, closed - is visible under one
 * reference, matching the staff-import ImportHistory pattern the user
 * asked to mirror. A row created at download time has no `uploaded_at`
 * yet; the upload step finds it again (by the reference embedded in the
 * spreadsheet itself, not the filename) and fills the rest in.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_roster_imports', function (Blueprint $table) {
            $table->string('reference_number')->unique()->nullable()->after('id');
            $table->string('status')->default('open')->after('reference_number');
            $table->foreignId('campus_id')->nullable()->after('university_id')->constrained()->nullOnDelete();
            $table->foreignId('program_id')->nullable()->after('campus_id')->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('graduation_year')->nullable()->after('program_id');
            $table->timestamp('downloaded_at')->nullable()->after('flagged');
            $table->timestamp('uploaded_at')->nullable()->after('downloaded_at');
            $table->timestamp('closed_at')->nullable()->after('uploaded_at');
            $table->foreignId('closed_by')->nullable()->after('closed_at')->constrained('staff')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_roster_imports', function (Blueprint $table) {
            $table->dropForeign(['campus_id']);
            $table->dropForeign(['program_id']);
            $table->dropForeign(['closed_by']);
            $table->dropColumn([
                'reference_number', 'status', 'campus_id', 'program_id', 'graduation_year',
                'downloaded_at', 'uploaded_at', 'closed_at', 'closed_by',
            ]);
        });
    }
};
