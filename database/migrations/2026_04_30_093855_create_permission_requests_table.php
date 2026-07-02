<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permission_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->enum('permission_type', ['time', 'location']);
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->decimal('duration_hours', 5, 2);
            $table->text('destination_address')->nullable();
            $table->decimal('distance_km', 10, 2)->nullable();
            $table->text('purpose');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->string('rejection_reason', 191)->nullable();
            $table->timestamps();
            
            $table->index('staff_id');
            $table->index('permission_type');
            $table->index('status');
            $table->index(['start_time', 'end_time']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('permission_requests');
    }
};