<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('import_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('role_code', 3);
            $table->string('sequence_number', 20);
            $table->string('operation_type', 20);
            $table->string('original_filename', 255);
            $table->string('stored_filename', 255);
            $table->integer('file_size')->nullable();
            $table->integer('total_rows')->default(0);
            $table->integer('successful_rows')->default(0);
            $table->integer('failed_rows')->default(0);
            $table->json('errors')->nullable();
            $table->enum('status', ['pending', 'processing', 'preview', 'completed', 'failed'])->default('pending');
            $table->boolean('is_test_mode')->default(false);
            $table->json('preview_data')->nullable();
            $table->string('error_report_path', 500)->nullable();
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('processing_completed_at')->nullable();
            $table->date('retention_expiry')->nullable();
            $table->timestamps();
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_history');
    }
};