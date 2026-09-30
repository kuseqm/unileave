<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('staff', function (Blueprint $table) {
            // The default ID card validity date carried over from staff_id_hr's
            // employees.valid_until — used to prefill the "Valid Until" field when
            // generating a card, not the issued card's own valid_until (that lives
            // on id_cards and can differ once a card is actually generated).
            $table->date('id_card_valid_until')->nullable()->after('avatar');
        });
    }

    public function down()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('id_card_valid_until');
        });
    }
};
