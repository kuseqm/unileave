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
        if (Schema::hasTable('staff_addresses')) {
            return;
        }

        Schema::create('staff_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->enum('address_type', ['permanent', 'current', 'postal', 'emergency'])->default('current');
            $table->string('street', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->default('Tanzania');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_addresses');
    }
};
