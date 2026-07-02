<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('request_type', ['leave', 'permission', 'admin']);
            $table->unsignedBigInteger('request_id');
            $table->string('from_role', 50);
            $table->string('to_role', 50);
            $table->enum('action', ['submitted', 'approved', 'rejected', 'forwarded', 'cancelled', 'assigned', 'override_balance']);
            $table->text('comment')->nullable();
            $table->foreignId('approver_staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->timestamps();
            
            $table->index(['request_type', 'request_id']);
            $table->index('approver_staff_id');
            $table->index('created_at');
            $table->index('action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('approval_logs');
    }
};