<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('certification_name', 255);
            $table->string('issuing_body', 255)->nullable();
            $table->string('certification_number', 100)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_renewable')->default(false);
            $table->string('certificate_file', 500)->nullable();
            $table->timestamps();

            $table->index('staff_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_certifications');
    }
};
