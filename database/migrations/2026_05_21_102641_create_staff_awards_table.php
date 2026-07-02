<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('award_name', 255);
            $table->string('awarding_body', 255)->nullable();
            $table->date('award_date')->nullable();
            $table->enum('award_level', ['international', 'national', 'institutional', 'departmental'])->nullable();
            $table->text('description')->nullable();
            $table->string('certificate_file', 500)->nullable();
            $table->timestamps();
            
            $table->index('staff_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_awards');
    }
};