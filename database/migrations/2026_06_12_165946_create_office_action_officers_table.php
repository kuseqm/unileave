<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('office_action_officers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('staff_id');
            $table->string('role_in_office', 50)->default('Officer');
            $table->boolean('is_default')->default(false);
            $table->boolean('can_approve')->default(false);
            $table->timestamps();

            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('users')->onDelete('cascade');

            $table->index('office_id');
            $table->index('staff_id');
            $table->index('is_default');
        });
    }

    public function down()
    {
        Schema::dropIfExists('office_action_officers');
    }
};