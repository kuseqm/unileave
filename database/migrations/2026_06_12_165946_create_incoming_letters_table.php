<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('incoming_letters', function (Blueprint $table) {
            $table->id();
            $table->string('ref_number', 25)->unique();
            $table->date('date_received');
            $table->string('sender', 200);
            $table->unsignedBigInteger('office_id');
            $table->text('subject');
            $table->string('pdf_path', 500);
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->enum('status', ['received', 'assigned', 'in_progress', 'replied', 'closed'])->default('received');
            $table->enum('priority', ['normal', 'urgent', 'confidential'])->default('normal');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->foreign('office_id')->references('id')->on('offices');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users');

            $table->index('ref_number');
            $table->index('status');
            $table->index('date_received');
            $table->index('office_id');
            $table->index('priority');
        });
    }

    public function down()
    {
        Schema::dropIfExists('incoming_letters');
    }
};
