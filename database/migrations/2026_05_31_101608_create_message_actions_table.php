<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('message_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // opened, replied, forwarded, actioned, escalated
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['message_id', 'action']);
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('message_actions');
    }
};