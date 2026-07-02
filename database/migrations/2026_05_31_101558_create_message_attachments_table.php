<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->integer('file_size'); // in bytes
            $table->string('mime_type');
            $table->string('disk')->default('public');
            $table->timestamps();
            
            $table->index('message_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('message_attachments');
    }
};