<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('short_name', 30)->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('campus_id')->nullable();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('head_staff_id')->nullable();
            $table->string('email_alias', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('receives_letters')->default(true);
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Foreign keys
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('set null');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
            $table->foreign('head_staff_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users');

            // Indexes
            $table->index('is_active');
            $table->index('department_id');
            $table->index('short_name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('offices');
    }
};