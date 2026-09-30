<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index('conversation_id');
            $table->index(['receiver_id', 'is_read']);
            // conversation_id -> message_conversations is added in a later migration:
            // that table is created AFTER this one runs, so the constraint can't be
            // defined here without breaking a fresh install.
            $table->foreign('sender_id')->references('id')->on('staff');
            $table->foreign('receiver_id')->references('id')->on('staff');
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
