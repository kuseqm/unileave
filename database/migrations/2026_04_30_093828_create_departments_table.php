<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('campus_id')->unsigned();
            $table->bigInteger('school_id')->unsigned()->nullable();  // ← REMOVED ->after()
            $table->string('name');
            $table->string('code', 50);
            $table->bigInteger('hod_staff_id')->unsigned()->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            // Foreign keys (optional but recommended)
            $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
            $table->foreign('hod_staff_id')->references('id')->on('staff')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('departments');
    }
};