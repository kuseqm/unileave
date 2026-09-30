<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('acting_dvc_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['dvca', 'dvc-pfa']);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason')->nullable();
            $table->foreignId('approved_by')->constrained('staff')->onDelete('cascade');
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();

            $table->index('staff_id');
            $table->index(['start_date', 'end_date']);
            $table->index('role');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('acting_dvc_assignments');
    }
};
