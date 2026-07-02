<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->boolean('email_notifications')->default(true);
            $table->boolean('in_app_notifications')->default(true);
            $table->boolean('acting_requests')->default(true);
            $table->boolean('approvals_pending')->default(true);
            $table->boolean('approval_updates')->default(true);
            $table->boolean('document_notifications')->default(true);
            $table->boolean('reminder_notifications')->default(true);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_preferences');
    }
};