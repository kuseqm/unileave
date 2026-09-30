<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            // Add encrypted columns for sensitive data
            $table->text('national_id_encrypted')->nullable()->after('national_id');
            $table->text('tin_number_encrypted')->nullable()->after('tin_number');
            $table->text('nssf_number_encrypted')->nullable()->after('nssf_number');
            $table->text('basic_salary_encrypted')->nullable()->after('basic_salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn([
                'national_id_encrypted',
                'tin_number_encrypted',
                'nssf_number_encrypted',
                'basic_salary_encrypted',
            ]);
        });
    }
};
