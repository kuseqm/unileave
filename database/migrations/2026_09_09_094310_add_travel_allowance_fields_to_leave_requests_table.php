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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->boolean('travel_allowance_granted')->default(false)->after('payment_decided_by');
            $table->decimal('travel_allowance_amount', 10, 2)->default(0)->after('travel_allowance_granted');
            $table->text('travel_allowance_notes')->nullable()->after('travel_allowance_amount');
            $table->timestamp('travel_allowance_decided_at')->nullable()->after('travel_allowance_notes');
            $table->foreignId('travel_allowance_decided_by')->nullable()->after('travel_allowance_decided_at')->constrained('staff')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['travel_allowance_decided_by']);
            $table->dropColumn([
                'travel_allowance_granted',
                'travel_allowance_amount',
                'travel_allowance_notes',
                'travel_allowance_decided_at',
                'travel_allowance_decided_by',
            ]);
        });
    }
};
