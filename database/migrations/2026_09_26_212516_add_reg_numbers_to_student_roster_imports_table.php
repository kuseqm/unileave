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
        Schema::table('student_roster_imports', function (Blueprint $table) {
            $table->json('created_reg_numbers')->nullable()->after('created_count');
            $table->json('updated_reg_numbers')->nullable()->after('updated_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_roster_imports', function (Blueprint $table) {
            $table->dropColumn(['created_reg_numbers', 'updated_reg_numbers']);
        });
    }
};
