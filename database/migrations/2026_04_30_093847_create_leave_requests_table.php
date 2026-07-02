<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days', 5, 2);
            $table->text('reason');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->string('rejection_reason', 191)->nullable();
            $table->integer('current_approval_level')->default(1);
            $table->text('letter_content')->nullable();
            $table->timestamps();
            
            $table->index('staff_id');
            $table->index('leave_type_id');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
            $table->index('current_approval_level');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_requests');
    }
};