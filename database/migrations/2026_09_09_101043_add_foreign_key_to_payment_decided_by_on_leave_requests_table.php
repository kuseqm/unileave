<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * `payment_decided_by` was originally added by
     * 2026_05_29_115019_add_payment_fields_to_leave_requests_table.php with
     * ->constrained('users') - but this app's User model lives on the
     * `staff` table, and the default `users` table is unused/empty. No
     * actual foreign key ended up on the column (confirmed via
     * information_schema before writing this), so it silently accepted any
     * integer. This adds the constraint that was meant to be there,
     * pointing at the correct table.
     *
     * A brand-new database is different: the original migration does create
     * its (mis-targeted) FK there, under the same auto-generated name this
     * one would use, so adding it blindly fails with "Duplicate foreign key
     * constraint name" and no fresh install could ever finish migrating.
     * Any FK already on the column is dropped first, then the correct one
     * is added - a no-op change for databases that had none.
     */
    public function up(): void
    {
        $existing = collect(Schema::getForeignKeys('leave_requests'))
            ->first(fn (array $fk) => $fk['columns'] === ['payment_decided_by']);

        Schema::table('leave_requests', function (Blueprint $table) use ($existing) {
            if ($existing) {
                $table->dropForeign($existing['name']);
            }

            $table->foreign('payment_decided_by')->references('id')->on('staff')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['payment_decided_by']);
        });
    }
};
