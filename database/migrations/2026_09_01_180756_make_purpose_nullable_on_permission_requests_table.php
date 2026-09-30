<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * `purpose` was the original required field before `reason` replaced it
     * (see 2026_05_18_104020_add_fields_to_permission_requests_table). The
     * controller only ever populates `reason`, so every insert has been
     * failing with "Field 'purpose' doesn't have a default value".
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE permission_requests MODIFY purpose TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE permission_requests MODIFY purpose TEXT NOT NULL');
    }
};
