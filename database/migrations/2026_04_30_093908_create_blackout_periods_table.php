<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blackout_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('restriction_type', ['hard', 'soft'])->default('hard');
            $table->json('affected_departments')->nullable();
            $table->json('affected_leave_types')->nullable();
            $table->json('affected_categories')->nullable();
            $table->boolean('override_allowed')->default(false);
            $table->foreignId('created_by')->constrained('staff')->onDelete('cascade');
            $table->timestamps();

            $table->index('university_id');
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('blackout_periods');
    }
};
