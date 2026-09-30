<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Raw SQL rather than Blueprint::change() - this app doesn't have
     * doctrine/dbal installed, which change() requires.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE documents MODIFY category_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE documents MODIFY category_id BIGINT UNSIGNED NOT NULL');
    }
};
