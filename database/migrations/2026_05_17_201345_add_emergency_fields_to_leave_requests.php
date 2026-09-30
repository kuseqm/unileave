<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Contact during leave
            $table->string('contact_number_during_leave', 50)->nullable();
            $table->string('leave_address', 255)->nullable();
            $table->string('travel_destination', 255)->nullable();
            $table->date('expected_return_date')->nullable();
            $table->enum('half_day', ['full', 'morning', 'afternoon'])->default('full');

            // Emergency contact for this leave period (can be linked or temporary)
            $table->unsignedBigInteger('emergency_contact_id')->nullable();
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 50)->nullable();
            $table->text('handover_notes')->nullable();

            // Foreign key to emergency contacts table
            $table->foreign('emergency_contact_id')
                ->references('id')
                ->on('staff_emergency_contacts')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['emergency_contact_id']);
            $table->dropColumn([
                'contact_number_during_leave',
                'leave_address',
                'travel_destination',
                'expected_return_date',
                'half_day',
                'emergency_contact_id',
                'emergency_contact_name',
                'emergency_contact_phone',
                'handover_notes',
            ]);
        });
    }
};
