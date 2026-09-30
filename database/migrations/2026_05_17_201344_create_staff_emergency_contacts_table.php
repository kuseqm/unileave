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
        // migration system entirely). The guard makes this migration safe to
        // run there too — it just gets marked "done" without recreating
        // anything — while still allowing a fresh install to create it here,
        // before add_emergency_fields_to_leave_requests needs to reference it.
        if (Schema::hasTable('staff_emergency_contacts')) {
            return;
        }

        Schema::create('staff_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->string('full_name', 100);
            $table->string('relationship', 50);
            $table->string('phone_primary', 50);
            $table->string('phone_secondary', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('address', 255)->nullable();
            $table->integer('priority')->default(1)->comment('1 = primary, 2 = secondary, etc.');
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
        Schema::dropIfExists('staff_emergency_contacts');
    }
};
