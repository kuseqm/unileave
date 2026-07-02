<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('letters', function (Blueprint $table) {
            // Add auto-numbering preference (default: true)
            $table->boolean('auto_number_paragraphs')->default(true)->after('content');
            
            // Store raw content before formatting (optional)
            $table->text('raw_content')->nullable()->after('auto_number_paragraphs');
        });
    }

    public function down()
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn(['auto_number_paragraphs', 'raw_content']);
        });
    }
};