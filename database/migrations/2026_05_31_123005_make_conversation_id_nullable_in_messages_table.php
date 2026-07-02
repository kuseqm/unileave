<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Make conversation_id nullable and add a default value option
            $table->unsignedBigInteger('conversation_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('conversation_id')->nullable(false)->change();
        });
    }
};