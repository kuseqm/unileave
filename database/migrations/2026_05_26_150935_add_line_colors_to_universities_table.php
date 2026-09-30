<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('universities', function (Blueprint $table) {
            // Line colors for letterhead
            $table->string('primary_line_color')->default('#000000')->after('secondary_color');
            $table->string('secondary_line_color')->default('#cccccc')->after('primary_line_color');
            $table->string('primary_line_thickness')->default('2px')->after('secondary_line_color');
            $table->string('secondary_line_thickness')->default('1px')->after('primary_line_thickness');
        });
    }

    public function down()
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropColumn([
                'primary_line_color',
                'secondary_line_color',
                'primary_line_thickness',
                'secondary_line_thickness',
            ]);
        });
    }
};
