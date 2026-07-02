<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('domain')->unique()->nullable(); // subdomain or custom domain
            $table->string('database')->unique()->nullable(); // if using separate database per tenant
            $table->string('status')->default('active'); // active, suspended, pending
            $table->json('settings')->nullable(); // JSON settings for tenant
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenants');
    }
};