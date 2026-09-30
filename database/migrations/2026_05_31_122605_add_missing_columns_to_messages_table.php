<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Add missing columns
            if (! Schema::hasColumn('messages', 'subject')) {
                $table->string('subject')->after('message_number')->nullable();
            }

            if (! Schema::hasColumn('messages', 'message')) {
                $table->longText('message')->after('subject');
            }

            if (! Schema::hasColumn('messages', 'priority')) {
                $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('message');
            }

            if (! Schema::hasColumn('messages', 'message_type')) {
                $table->string('message_type')->default('general')->after('priority');
            }

            if (! Schema::hasColumn('messages', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('messages', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('sent_at');
            }

            // Change recipient_ids to JSON (not string)
            if (Schema::hasColumn('messages', 'recipient_ids')) {
                $table->json('recipient_ids')->change();
            } else {
                $table->json('recipient_ids')->nullable()->after('receiver_id');
            }
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['subject', 'message', 'priority', 'message_type', 'sent_at', 'delivered_at']);
        });
    }
};
