<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('language_name', 100);
            $table->enum('proficiency_read', ['none', 'basic', 'intermediate', 'fluent', 'native'])->default('none');
            $table->enum('proficiency_write', ['none', 'basic', 'intermediate', 'fluent', 'native'])->default('none');
            $table->enum('proficiency_speak', ['none', 'basic', 'intermediate', 'fluent', 'native'])->default('none');
            $table->boolean('is_working_language')->default(false);
            $table->timestamps();

            $table->index('staff_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_languages');
    }
};
