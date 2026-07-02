<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->enum('payment_status', ['paid', 'partial', 'unpaid'])->default('paid')->after('status');
            $table->decimal('payment_percentage', 5, 2)->default(100)->after('payment_status');
            $table->text('payment_notes')->nullable()->after('payment_percentage');
            $table->timestamp('payment_decision_at')->nullable()->after('payment_notes');
            $table->foreignId('payment_decided_by')->nullable()->after('payment_decision_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['payment_decided_by']);
            $table->dropColumn([
                'payment_status',
                'payment_percentage',
                'payment_notes',
                'payment_decision_at',
                'payment_decided_by'
            ]);
        });
    }
};