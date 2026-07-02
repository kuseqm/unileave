<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', [
                'acting_request', 'approval_pending', 'approval_granted', 'approval_rejected',
                'hold_accepted', 'hold_declined', 'leave_cancelled', 'document_uploaded',
                'leave_approved_final', 'balance_updated', 'reminder'
            ]);
            $table->string('title', 255);
            $table->text('message');
            $table->json('data')->nullable();
            $table->string('action_url', 255)->nullable();
            $table->string('action_text', 100)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_read']);
            $table->foreign('user_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};