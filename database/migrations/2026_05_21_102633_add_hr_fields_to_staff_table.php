<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('staff', function (Blueprint $table) {
            // Core HR Fields
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('national_id', 50)->nullable()->after('date_of_birth');
            $table->string('job_title')->nullable()->after('staff_category');
            $table->string('qualification')->nullable()->after('job_title');

            // Contract Information
            $table->enum('contract_type', ['permanent', 'contract', 'temporary', 'probation'])->nullable()->after('joining_date');
            $table->date('contract_start_date')->nullable()->after('contract_type');
            $table->date('contract_end_date')->nullable()->after('contract_start_date');
            $table->date('confirmation_date')->nullable()->after('contract_end_date');

            // Financial
            $table->string('job_grade', 50)->nullable()->after('job_title');
            $table->decimal('basic_salary', 15, 2)->nullable()->after('job_grade');
            $table->string('tin_number', 50)->nullable()->after('basic_salary');
            $table->string('nssf_number', 50)->nullable()->after('tin_number');

            // Employment Status
            $table->enum('employment_status', ['active', 'on_leave', 'suspended', 'terminated', 'retired'])->default('active')->after('status');
            $table->date('resignation_date')->nullable()->after('employment_status');
            $table->text('resignation_reason')->nullable()->after('resignation_date');
        });
    }

    public function down()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth', 'national_id', 'job_title', 'qualification',
                'contract_type', 'contract_start_date', 'contract_end_date', 'confirmation_date',
                'job_grade', 'basic_salary', 'tin_number', 'nssf_number',
                'employment_status', 'resignation_date', 'resignation_reason',
            ]);
        });
    }
};
