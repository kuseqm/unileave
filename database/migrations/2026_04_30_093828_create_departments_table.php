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

            // Foreign keys
            // campus_id -> campuses is safe here (campuses migration runs first).
            // school_id -> schools and hod_staff_id -> staff are added in a later
            // migration instead: both of those tables are created AFTER this one
            // runs, so defining those constraints here breaks a fresh install.
            $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('departments');
    }
};
