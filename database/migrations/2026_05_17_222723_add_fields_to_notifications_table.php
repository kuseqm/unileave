<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'icon')) {
                $table->string('icon', 50)->nullable()->after('action_text');
            }
            if (!Schema::hasColumn('notifications', 'icon_color')) {
                $table->string('icon_color', 30)->nullable()->after('icon');
            }
            if (!Schema::hasColumn('notifications', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('read_at');
            }
            if (!Schema::hasColumn('notifications', 'sent_via')) {
                $table->string('sent_via', 100)->nullable()->after('sent_at');
            }
            if (!Schema::hasColumn('notifications', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('sent_via');
            }
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['icon', 'icon_color', 'sent_at', 'sent_via', 'priority']);
        });
    }
};