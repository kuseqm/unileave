<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('letters', function (Blueprint $table) {
            // Multiple recipients support
            $table->json('multiple_recipients')->nullable()->after('recipient_address');
            $table->boolean('has_multiple_recipients')->default(false)->after('multiple_recipients');
        });
    }

    public function down()
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn([
                'multiple_recipients',
                'has_multiple_recipients',
            ]);
        });
    }
};
