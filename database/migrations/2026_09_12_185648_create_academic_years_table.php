<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * An academic year IS the leave year - the same start/end dates drive
     * both the academic calendar shown across the system and the leave
     * balance/leave-counter window (see resources/views/components/
     * leave-counter.blade.php, which previously hardcoded a Sept 1 - Aug 31
     * window instead of reading a real record).
     */
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('label')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('semester_1_name')->default('Semester I');
            $table->date('semester_1_start');
            $table->date('semester_1_end');
            $table->string('semester_2_name')->default('Semester II');
            $table->date('semester_2_start');
            $table->date('semester_2_end');
            $table->unsignedTinyInteger('current_semester')->default(1);
            $table->boolean('is_current')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
