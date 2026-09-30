<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents');
            $table->string('verification_code')->unique();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('verification_data')->nullable(); // extra info
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_verifications');
    }
};
