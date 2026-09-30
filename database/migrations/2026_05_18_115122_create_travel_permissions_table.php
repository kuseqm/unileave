<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('travel_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('acting_staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('destination', 255);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days')->default(0);
            $table->enum('travel_reason', ['official', 'personal']);
            $table->text('official_benefit')->nullable();
            $table->text('personal_description')->nullable();
            $table->enum('cost_coverage', ['organizers_full', 'organizers_partial', 'university', 'private']);
            $table->text('additional_details')->nullable();

            // Acting staff status
            $table->enum('acting_status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamp('acting_responded_at')->nullable();
            $table->string('acting_token', 100)->nullable();
            $table->text('acting_comment')->nullable();

            // Approval status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('current_approval_level')->default('acting');
            $table->text('rejection_reason')->nullable();

            // Evidence attachments
            $table->string('invitation_letter_path')->nullable();
            $table->string('endorsement_path')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('staff_id');
            $table->index('acting_staff_id');
            $table->index('status');
            $table->index('current_approval_level');
        });
    }

    public function down()
    {
        Schema::dropIfExists('travel_permissions');
    }
};
