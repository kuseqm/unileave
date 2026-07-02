<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('report_definition_id')->constrained('report_definitions');
            $table->string('format'); // pdf, excel, html
            $table->json('filters_applied')->nullable();
            $table->string('file_path')->nullable();
            $table->string('qr_code')->nullable(); // unique identifier for verification
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_logs');
    }
};