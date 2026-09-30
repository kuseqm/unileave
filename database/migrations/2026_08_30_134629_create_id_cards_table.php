<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('id_cards', function (Blueprint $table) {
            $table->id();
            // Plain indexed column, no FK constraint — matches the rest of this
            // database (e.g. staff_addresses.staff_id): staff/related tables run
            // on MyISAM here, which doesn't support foreign keys.
            $table->unsignedBigInteger('staff_id');
            $table->date('issued_at')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('authorized_by')->nullable();
            $table->string('signature_path')->nullable();
            $table->unsignedBigInteger('authorized_signature_id')->nullable();
            $table->timestamps();

            $table->index('staff_id');
            $table->index('authorized_signature_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('id_cards');
    }
};
