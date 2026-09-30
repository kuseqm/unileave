<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id', 50)->unique();
            $table->string('title', 20)->nullable();  // ✅ ADDED
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();  // ✅ ADDED
            $table->string('last_name', 100);
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->foreignId('campus_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('reporting_to')->nullable();  // ✅ ADDED
            $table->string('email', 191)->unique();
            $table->string('password', 191);
            $table->enum('staff_category', ['academic', 'administrative', 'supporting']);
            $table->date('joining_date');
            $table->string('phone', 50)->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->boolean('password_change_required')->default(true);
            $table->string('current_role', 50)->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->string('avatar', 255)->nullable();  // ✅ ADDED
            $table->rememberToken();
            $table->timestamps();

            // Foreign key for reporting_to
            $table->foreign('reporting_to')->references('id')->on('staff')->onDelete('set null');

            $table->index('university_id');
            $table->index('campus_id');
            $table->index('department_id');
            $table->index('staff_category');
            $table->index('status');
            $table->index('email');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff');
    }
};
