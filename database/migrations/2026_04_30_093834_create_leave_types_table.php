<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            
            // ✅ ADD THIS - Annual entitlement days
            $table->integer('days_allowed')->nullable();
            
            $table->boolean('requires_certificate')->default(false);
            $table->boolean('requires_letter')->default(false);
            $table->boolean('deduct_balance')->default(true);
            $table->boolean('vc_discretion')->default(false);
            $table->integer('max_consecutive_days')->nullable()->default(28);
            $table->boolean('is_configurable')->default(false);
            $table->json('config_fields')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_types');
    }
};