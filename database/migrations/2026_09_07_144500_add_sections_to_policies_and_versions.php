<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            if (! Schema::hasColumn('policies', 'sections')) {
                $table->json('sections')->nullable()->after('content');
            }
        });

        Schema::table('policy_versions', function (Blueprint $table) {
            if (! Schema::hasColumn('policy_versions', 'sections')) {
                $table->json('sections')->nullable()->after('content');
            }
        });
    }

    public function down(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            if (Schema::hasColumn('policies', 'sections')) {
                $table->dropColumn('sections');
            }
        });

        Schema::table('policy_versions', function (Blueprint $table) {
            if (Schema::hasColumn('policy_versions', 'sections')) {
                $table->dropColumn('sections');
            }
        });
    }
};
