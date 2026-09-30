<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Return to Work fields
            $table->date('actual_return_date')->nullable()->after('expected_return_date');
            $table->integer('late_return_days')->default(0)->after('actual_return_date');
            $table->decimal('late_return_penalty', 10, 2)->default(0)->after('late_return_days');
            $table->text('late_return_reason')->nullable()->after('late_return_penalty');

            // Financial fields
            $table->boolean('is_paid_leave')->default(true)->after('late_return_reason');
            $table->decimal('salary_impact', 10, 2)->default(0)->after('is_paid_leave');
            $table->text('allowances')->nullable()->after('salary_impact');

            // Dependents fields
            $table->integer('dependents_count')->default(0)->after('allowances');
            $table->text('dependents_info')->nullable()->after('dependents_count');
        });
    }

    public function down()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn([
                'actual_return_date',
                'late_return_days',
                'late_return_penalty',
                'late_return_reason',
                'is_paid_leave',
                'salary_impact',
                'allowances',
                'dependents_count',
                'dependents_info',
            ]);
        });
    }
};
