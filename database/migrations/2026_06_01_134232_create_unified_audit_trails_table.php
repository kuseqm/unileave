<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('unified_audit_trails', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type'); // Message, LeaveRequest, PermissionRequest, TravelPermission, Letter
            $table->unsignedBigInteger('auditable_id');
            $table->string('event'); // created, updated, deleted, restored, viewed, replied, approved, rejected, accessed_via_code
            $table->string('action'); // Human readable action
            $table->foreignId('user_id')->constrained('staff');
            $table->string('user_role')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable(); // Additional context like reason, one-time-code-used, etc.
            $table->timestamps();
            
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('event');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('unified_audit_trails');
    }
};