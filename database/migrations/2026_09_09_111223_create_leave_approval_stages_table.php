<?php

use App\Models\LeaveRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Seeds the current hardcoded chain order (HOD -> Dean -> HR -> DVC ->
     * HR Logistics) as data, so nothing changes behaviorally until an admin
     * actually reorders/disables a stage on the new config page.
     */
    public function up(): void
    {
        Schema::create('leave_approval_stages', function (Blueprint $table) {
            $table->id();
            // Matches LeaveRequest::LEVEL_* - fixed set of 5 stage types,
            // each with its own already-working scope-resolution logic
            // (who exactly is "the HOD" for a given applicant, etc). Only
            // the order and whether a stage participates is configurable -
            // new arbitrary stage types are out of scope.
            $table->string('stage_key', 20)->unique();
            $table->unsignedTinyInteger('level')->unique();
            $table->string('display_name', 50);
            $table->unsignedTinyInteger('sequence_order');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        DB::table('leave_approval_stages')->insert([
            ['stage_key' => 'hod', 'level' => LeaveRequest::LEVEL_HOD, 'display_name' => 'Head of Department', 'sequence_order' => 1, 'is_enabled' => true, 'created_at' => now(), 'updated_at' => now()],
            ['stage_key' => 'dean', 'level' => LeaveRequest::LEVEL_DEAN, 'display_name' => 'Dean of Faculty', 'sequence_order' => 2, 'is_enabled' => true, 'created_at' => now(), 'updated_at' => now()],
            ['stage_key' => 'hr', 'level' => LeaveRequest::LEVEL_HR, 'display_name' => 'Human Resources', 'sequence_order' => 3, 'is_enabled' => true, 'created_at' => now(), 'updated_at' => now()],
            ['stage_key' => 'dvc', 'level' => LeaveRequest::LEVEL_DVC, 'display_name' => 'DVC', 'sequence_order' => 4, 'is_enabled' => true, 'created_at' => now(), 'updated_at' => now()],
            ['stage_key' => 'hr_logistics', 'level' => LeaveRequest::LEVEL_HR_LOGISTICS, 'display_name' => 'HR Logistics', 'sequence_order' => 5, 'is_enabled' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_approval_stages');
    }
};
