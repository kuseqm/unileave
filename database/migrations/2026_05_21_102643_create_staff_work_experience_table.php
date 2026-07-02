<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_work_experience', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('employer_name', 255);
            $table->string('job_title', 255)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('responsibilities')->nullable();
            $table->text('reason_for_leaving')->nullable();
            $table->string('supervisor_name', 255)->nullable();
            $table->string('supervisor_contact', 100)->nullable();
            $table->timestamps();
            
            $table->index('staff_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_work_experience');
    }
};