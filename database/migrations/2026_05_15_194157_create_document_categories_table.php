<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->default('ti-file');
            $table->json('required_for_leave_types')->nullable();
            $table->integer('max_size_mb')->default(5);
            $table->json('allowed_types')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_categories');
    }
};