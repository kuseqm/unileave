<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('print_queue', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outgoing_letter_id');
            $table->text('recipient_address');
            $table->unsignedBigInteger('printed_by')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->unsignedBigInteger('dispatched_by')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->enum('status', ['pending', 'printed', 'dispatched'])->default('pending');
            $table->timestamps();

            $table->foreign('outgoing_letter_id')->references('id')->on('outgoing_letters')->onDelete('cascade');
            $table->foreign('printed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('dispatched_by')->references('id')->on('users')->onDelete('set null');

            $table->index('status');
            $table->index('outgoing_letter_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('print_queue');
    }
};