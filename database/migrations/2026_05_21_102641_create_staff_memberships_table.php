<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('organization_name', 255);
            $table->string('membership_number', 100)->nullable();
            $table->enum('membership_type', ['fellow', 'senior', 'full', 'associate', 'student'])->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('certificate_file', 500)->nullable();
            $table->timestamps();

            $table->index('staff_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_memberships');
    }
};
