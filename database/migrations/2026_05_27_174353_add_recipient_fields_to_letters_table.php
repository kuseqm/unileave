<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('letters', function (Blueprint $table) {
            // Recipient custom address (user-input, for external recipients)
            $table->text('recipient_address')->nullable()->after('recipient_id');
            
            // Flag to determine which to use (true = custom address, false = staff relationship)
            $table->boolean('use_custom_recipient')->default(false)->after('recipient_address');
            
            // Optional: Recipient name for custom addresses (extracted from address for quick reference)
            $table->string('recipient_name')->nullable()->after('use_custom_recipient');
            
            // Optional: Recipient type (ministry, partner, external, etc.)
            $table->string('recipient_type')->nullable()->after('recipient_name');
        });
    }

    public function down()
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn([
                'recipient_address',
                'use_custom_recipient',
                'recipient_name',
                'recipient_type',
            ]);
        });
    }
};