<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('code', 50)->unique();
            $table->decimal('gps_lat', 10, 8)->nullable();
            $table->decimal('gps_lng', 11, 8)->nullable();
            $table->string('timezone', 50)->default('Africa/Dar_es_Salaam');
            $table->string('logo_path', 191)->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            $table->index('status');
            $table->index('code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('universities');
    }
};
