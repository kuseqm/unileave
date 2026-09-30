<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets staff review each import update instead of silently trusting it.
 *
 * `import_diff` (existing) is the human-readable, grouped diff shown on the
 * Recent Updates pages - it merges last_name/first_name/middle_name into a
 * single "name" entry and cannot be replayed field-by-field. `import_diff_raw`
 * stores the same update as raw column => old value pairs, captured before
 * that grouping happens, so Reject can restore each column exactly rather
 * than guessing from the display text.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_graduations', function (Blueprint $table) {
            $table->json('import_diff_raw')->nullable()->after('import_diff');
            $table->string('import_review_status')->nullable()->after('import_diff_raw');
            $table->foreignId('import_reviewed_by')->nullable()->after('import_review_status')->constrained('staff')->nullOnDelete();
            $table->timestamp('import_reviewed_at')->nullable()->after('import_reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('student_graduations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('import_reviewed_by');
            $table->dropColumn(['import_diff_raw', 'import_review_status', 'import_reviewed_at']);
        });
    }
};
