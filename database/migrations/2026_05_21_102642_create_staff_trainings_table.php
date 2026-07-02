<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('training_title', 255);
            $table->string('training_provider', 255)->nullable();
            $table->enum('training_type', ['internal', 'external', 'online', 'international'])->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('duration_days')->nullable();
            $table->string('certificate_file', 500)->nullable();
            $table->timestamps();
            
            $table->index('staff_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_trainings');
    }
};