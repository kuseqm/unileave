<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 10, 2)->default(0);
            $table->integer('year');
            $table->decimal('carried_forward', 10, 2)->default(0);
            $table->foreignId('adjusted_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->string('adjustment_reason', 191)->nullable();
            $table->timestamps();

            $table->unique(['staff_id', 'leave_type_id', 'year'], 'uk_leave_balances');
            $table->index('staff_id');
            $table->index('year');
            $table->index('leave_type_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_balances');
    }
};
