<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A policy `code` was unique across the whole platform, which meant a
     * university could never hold its own copy of a platform policy (e.g.
     * its own HR-LEAVE-001 overriding the platform default) - the code was
     * already taken. Uniqueness now applies per university instead.
     *
     * MySQL treats NULLs as distinct inside a unique index, so two platform
     * (university_id IS NULL) policies with the same code are not stopped
     * here; PolicyController's validation enforces that case.
     */
    public function up(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->unique(['university_id', 'code'], 'policies_university_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropUnique('policies_university_code_unique');
            $table->unique('code');
        });
    }
};
