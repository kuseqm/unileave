<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            // FIRST: Add leave_request_id if it doesn't exist
            if (!Schema::hasColumn('messages', 'leave_request_id')) {
                $table->foreignId('leave_request_id')->nullable()->constrained()->after('id');
            }
            
            // Message reference number
            if (!Schema::hasColumn('messages', 'message_number')) {
                $table->string('message_number')->nullable()->after('leave_request_id');
            }
            
            // Enhanced type and priority
            if (!Schema::hasColumn('messages', 'message_type')) {
                $table->enum('message_type', [
                    'general', 'minute', 'approval', 'task', 'escalation', 'notification'
                ])->default('general')->after('message');
            }
            
            if (!Schema::hasColumn('messages', 'priority')) {
                $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('message_type');
            }
            
            // Enhanced status tracking
            if (!Schema::hasColumn('messages', 'status')) {
                $table->enum('status', [
                    'draft', 'sent', 'delivered', 'read', 'replied', 'actioned'
                ])->default('sent')->after('priority');
            }
            
            // Additional timestamps
            if (!Schema::hasColumn('messages', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('read_at');
            }
            
            if (!Schema::hasColumn('messages', 'replied_at')) {
                $table->timestamp('replied_at')->nullable()->after('delivered_at');
            }
            
            if (!Schema::hasColumn('messages', 'deadline')) {
                $table->timestamp('deadline')->nullable()->after('replied_at');
            }
            
            // User flags
            if (!Schema::hasColumn('messages', 'is_archived')) {
                $table->boolean('is_archived')->default(false)->after('is_read');
            }
            
            if (!Schema::hasColumn('messages', 'is_starred')) {
                $table->boolean('is_starred')->default(false)->after('is_archived');
            }
            
            // For reply threading
            if (!Schema::hasColumn('messages', 'parent_message_id')) {
                $table->foreignId('parent_message_id')->nullable()->constrained('messages')->after('attachments');
            }
            
            // Links to other systems
            if (!Schema::hasColumn('messages', 'related_incoming_mail_id')) {
                $table->foreignId('related_incoming_mail_id')->nullable()->after('leave_request_id');
            }
            
            if (!Schema::hasColumn('messages', 'related_outgoing_mail_id')) {
                $table->foreignId('related_outgoing_mail_id')->nullable()->after('related_incoming_mail_id');
            }
            
            if (!Schema::hasColumn('messages', 'related_file_id')) {
                $table->foreignId('related_file_id')->nullable()->after('related_outgoing_mail_id');
            }
        });
        
        // Add indexes after columns are added
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['receiver_id', 'status', 'priority']);
            $table->index(['sender_id', 'created_at']);
            $table->index('message_number');
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn([
                'leave_request_id', 'message_number', 'message_type', 'priority', 'status',
                'delivered_at', 'replied_at', 'deadline', 'is_archived', 'is_starred',
                'parent_message_id', 'related_incoming_mail_id', 'related_outgoing_mail_id', 'related_file_id'
            ]);
            $table->dropIndex(['receiver_id', 'status', 'priority']);
            $table->dropIndex(['sender_id', 'created_at']);
            $table->dropIndex(['message_number']);
        });
    }
};