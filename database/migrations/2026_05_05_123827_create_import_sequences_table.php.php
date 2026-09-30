<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('import_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('role_code', 3)->unique();
            $table->integer('last_sequence')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_sequences');
    }
};
