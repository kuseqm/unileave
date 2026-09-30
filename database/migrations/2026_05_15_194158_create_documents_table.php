<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('leave_request_id')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->string('original_name', 255);
            $table->string('stored_name', 255);
            $table->string('file_path', 500);
            $table->bigInteger('file_size')->unsigned()->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->enum('file_type', ['pdf', 'jpg', 'png', 'doc', 'docx']);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('uploaded_by');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index('leave_request_id');
            $table->index('user_id');
            $table->index('verification_status');
            $table->foreign('user_id')->references('id')->on('staff')->onDelete('cascade');
            $table->foreign('leave_request_id')->references('id')->on('leave_requests')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('document_categories');
            $table->foreign('uploaded_by')->references('id')->on('staff');
            $table->foreign('verified_by')->references('id')->on('staff');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
