<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_referees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('referee_name', 255);
            $table->string('referee_title', 100)->nullable();
            $table->string('referee_organization', 255)->nullable();
            $table->string('referee_email', 255)->nullable();
            $table->string('referee_phone', 20)->nullable();
            $table->string('relationship', 100)->nullable();
            $table->integer('years_known')->nullable();
            $table->boolean('is_contact_allowed')->default(true);
            $table->timestamps();
            
            $table->index('staff_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_referees');
    }
};