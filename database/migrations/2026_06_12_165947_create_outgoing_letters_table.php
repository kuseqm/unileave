<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('outgoing_letters', function (Blueprint $table) {
            $table->id();
            $table->string('ref_number', 25)->unique();
            $table->date('date_sent');
            $table->string('recipient', 200);
            $table->text('subject');
            $table->string('pdf_path', 500);
            $table->enum('dispatched_via', ['email', 'post', 'hand'])->default('email');
            $table->unsignedBigInteger('incoming_letter_id')->nullable();
            $table->unsignedBigInteger('drafted_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'dispatched'])->default('draft');
            $table->text('approval_comments')->nullable();
            $table->boolean('requires_print')->default(false);
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamps();

            $table->foreign('incoming_letter_id')->references('id')->on('incoming_letters')->onDelete('set null');
            $table->foreign('drafted_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');

            $table->index('ref_number');
            $table->index('status');
            $table->index('requires_print');
            $table->index('incoming_letter_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('outgoing_letters');
    }
};
