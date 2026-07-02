<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('import_history', function (Blueprint $table) {
            if (!Schema::hasColumn('import_history', 'stored_filename')) {
                $table->string('stored_filename')->nullable()->after('original_filename');
            }
        });
    }

    public function down()
    {
        Schema::table('import_history', function (Blueprint $table) {
            $table->dropColumn('stored_filename');
        });
    }
};