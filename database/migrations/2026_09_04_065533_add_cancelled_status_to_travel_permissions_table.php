<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Travel Permission has no self-service cancel, unlike Leave and
     * Permission - the status enum never had a 'cancelled' value to support
     * it. Widening it to bring Travel to parity with the other two.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE travel_permissions MODIFY status ENUM(
            'pending','approved','rejected','cancelled'
        ) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE travel_permissions MODIFY status ENUM(
            'pending','approved','rejected'
        ) NOT NULL DEFAULT 'pending'");
    }
};
