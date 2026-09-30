<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('message_access_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('staff')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->text('reason');
            $table->string('status')->default('pending'); // pending, approved, rejected, expired
            $table->string('one_time_code')->nullable();
            $table->timestamp('code_expires_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('message_access_requests');
    }
};
