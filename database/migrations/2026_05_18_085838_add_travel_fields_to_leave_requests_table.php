<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_requests', 'benefit_to_university')) {
                $table->text('benefit_to_university')->nullable()->after('handover_notes');
            }
            if (!Schema::hasColumn('leave_requests', 'cost_coverage')) {
                $table->enum('cost_coverage', ['organizers_full', 'organizers_partial', 'sjut', 'private'])->nullable()->after('benefit_to_university');
            }
            if (!Schema::hasColumn('leave_requests', 'is_first_time_applicant')) {
                $table->boolean('is_first_time_applicant')->default(false)->after('cost_coverage');
            }
        });
    }

    public function down()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['benefit_to_university', 'cost_coverage', 'is_first_time_applicant']);
        });
    }
};