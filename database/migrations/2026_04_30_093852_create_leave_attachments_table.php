<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_request_id')->constrained()->onDelete('cascade');
            $table->string('file_name', 191);
            $table->string('file_path', 500);
            $table->enum('file_type', ['certificate', 'letter', 'other']);
            $table->string('mime_type', 100);
            $table->integer('size_bytes');
            $table->timestamps();

            $table->index('leave_request_id');
            $table->index('file_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_attachments');
    }
};
