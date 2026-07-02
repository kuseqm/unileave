<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('message_conversations', function (Blueprint $table) {
            // Conversation reference number
            if (!Schema::hasColumn('message_conversations', 'conversation_number')) {
                $table->string('conversation_number')->nullable()->after('id');
            }
            
            // Subject line for the conversation
            if (!Schema::hasColumn('message_conversations', 'subject')) {
                $table->string('subject')->nullable()->after('participant_ids');
            }
            
            // Enhanced status
            if (!Schema::hasColumn('message_conversations', 'status')) {
                $table->enum('status', ['active', 'archived', 'closed'])->default('active')->after('is_active');
            }
            
            // Who closed it and when
            if (!Schema::hasColumn('message_conversations', 'closed_by')) {
                $table->foreignId('closed_by')->nullable()->constrained('users')->after('status');
            }
            
            if (!Schema::hasColumn('message_conversations', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('closed_by');
            }
        });
        
        // Add indexes
        Schema::table('message_conversations', function (Blueprint $table) {
            $table->index('conversation_number');
            $table->index(['status', 'updated_at']);
        });
    }

    public function down()
    {
        Schema::table('message_conversations', function (Blueprint $table) {
            $table->dropColumn(['conversation_number', 'subject', 'status', 'closed_by', 'closed_at']);
            $table->dropIndex(['conversation_number']);
            $table->dropIndex(['status', 'updated_at']);
        });
    }
};