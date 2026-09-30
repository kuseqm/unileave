<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a university's premium/opt-in features be turned on individually,
 * per institution - starting with "academic_records" (Students /
 * Graduation Verification). A simple string array in one JSON column
 * rather than a dedicated table: cheap to add a new add-on key later
 * (no new migration needed), and this is the first one - a richer
 * table (with an enabled_at/enabled_by audit trail, useful once billing
 * matters) is a reasonable upgrade path later, not needed yet.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->json('enabled_addons')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropColumn('enabled_addons');
        });
    }
};
