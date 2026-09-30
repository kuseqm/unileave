<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('phone_number', 20);
            $table->enum('phone_type', ['mobile', 'work', 'home', 'alternative', 'emergency'])->default('mobile');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index('staff_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_phones');
    }
};
