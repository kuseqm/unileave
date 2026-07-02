<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add tenant_id to staff table
        Schema::table('staff', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
        });

        // Add tenant_id to departments table
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
        });

        // Add tenant_id to schools table
        Schema::table('schools', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
        });

        // Add tenant_id to campuses table
        Schema::table('campuses', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
        });

        // Add tenant_id to universities table
        Schema::table('universities', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
        });

        // Add tenant_id to leave_requests table
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
        });

        // Add tenant_id to users table if not already
        if (!Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
        Schema::table('campuses', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
        Schema::table('universities', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};