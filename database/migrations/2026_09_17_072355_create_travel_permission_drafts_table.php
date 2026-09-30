<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('travel_permission_drafts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id')->unique();
            $table->unsignedTinyInteger('current_step')->default(1);
            $table->json('form_data')->nullable();
            $table->longText('signature_data')->nullable();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_permission_drafts');
    }
};
