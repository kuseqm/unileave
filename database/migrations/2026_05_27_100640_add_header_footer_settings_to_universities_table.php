<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('universities', function (Blueprint $table) {
            // Header & Footer Background
            $table->string('header_footer_bg_color')->default('#f8f9fa')->after('secondary_color');
            $table->string('footer_text')->nullable()->after('header_footer_bg_color');

            // University Name Color
            $table->string('uni_name_color')->default('#000000')->after('uni_name_font_weight');
        });
    }

    public function down()
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropColumn([
                'header_footer_bg_color',
                'footer_text',
                'uni_name_color',
            ]);
        });
    }
};
