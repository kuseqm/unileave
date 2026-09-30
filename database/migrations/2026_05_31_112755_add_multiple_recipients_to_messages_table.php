<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Add JSON column for multiple recipients
            if (! Schema::hasColumn('messages', 'recipient_ids')) {
                $table->json('recipient_ids')->nullable()->after('receiver_id');
            }

            // Make receiver_id nullable (since we'll use recipient_ids)
            $table->unsignedBigInteger('receiver_id')->nullable()->change();
        });

        // REMOVED: $table->index('recipient_ids'); - MySQL doesn't support JSON column indexing
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('recipient_ids');
            $table->unsignedBigInteger('receiver_id')->nullable(false)->change();
        });
    }
};
