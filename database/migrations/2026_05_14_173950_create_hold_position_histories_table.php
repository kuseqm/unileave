<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hold_position_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leave_request_id');
            $table->unsignedBigInteger('requester_staff_id');  // Staff who requested
            $table->unsignedBigInteger('holder_staff_id');     // Staff who held position
            $table->enum('status', ['pending', 'accepted', 'declined']);
            $table->timestamp('requested_at');
            $table->timestamp('responded_at')->nullable();
            $table->string('token')->unique();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('leave_request_id')->references('id')->on('leave_requests')->onDelete('cascade');
            $table->foreign('requester_staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->foreign('holder_staff_id')->references('id')->on('staff')->onDelete('cascade');

            $table->index(['requester_staff_id', 'holder_staff_id']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hold_position_histories');
    }
};
