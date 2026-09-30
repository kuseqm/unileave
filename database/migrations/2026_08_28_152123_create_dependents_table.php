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
        if (Schema::hasTable('dependents')) {
            return;
        }

        Schema::create('dependents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->string('full_name', 100);
            $table->enum('relationship', ['spouse', 'child', 'parent', 'sibling', 'other'])->default('other');
            $table->date('date_of_birth')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->boolean('is_emergency_contact')->default(false);
            $table->boolean('is_beneficiary')->default(false);
            $table->integer('beneficiary_percentage')->nullable()->comment('Percentage for inheritance/benefits');
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('dependents');
    }
};
