<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('qualification_name', 255);
            $table->string('institution', 255)->nullable();
            $table->string('country', 100)->nullable();
            $table->year('year_obtained')->nullable();
            $table->string('grade', 50)->nullable();
            $table->enum('qualification_type', ['phd', 'masters', 'bachelors', 'diploma', 'certificate', 'professional'])->nullable();
            $table->boolean('is_highest')->default(false);
            $table->string('certificate_file', 500)->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index('staff_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_qualifications');
    }
};