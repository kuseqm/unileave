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
        // Guarded: this table already exists on databases created before this
        // migration was written (it was previously created outside the
        // migration system entirely, like staff_emergency_contacts).
        if (Schema::hasTable('staff_bank_details')) {
            return;
        }

        Schema::create('staff_bank_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->string('bank_name', 100);
            $table->string('account_name', 100);
            $table->string('account_number', 50);
            $table->string('branch', 100)->nullable();
            $table->string('swift_code', 20)->nullable();
            $table->string('tin_number', 50)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['staff_id', 'account_number']);
            $table->index('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_bank_details');
    }
};
