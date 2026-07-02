<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('message_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('staff')->onDelete('cascade');
            $table->string('action'); // viewed, replied, archived, deleted, restored, accessed_via_code
            $table->string('access_method')->nullable(); // normal, admin, one_time_code
            $table->string('one_time_code_used')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('message_audit_logs');
    }
};