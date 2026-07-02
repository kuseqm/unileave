<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('message_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leave_request_id')->nullable();
            $table->unsignedBigInteger('initiator_id');
            $table->json('participant_ids');
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Remove JSON index - this was the issue
            $table->index('is_active');
            $table->foreign('leave_request_id')->references('id')->on('leave_requests')->onDelete('set null');
            $table->foreign('initiator_id')->references('id')->on('staff');
        });
    }

    public function down()
    {
        Schema::dropIfExists('message_conversations');
    }
};