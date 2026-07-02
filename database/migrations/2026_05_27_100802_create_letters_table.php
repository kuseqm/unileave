<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('letters')) {
            Schema::create('letters', function (Blueprint $table) {
                $table->id();
                
                // Polymorphic relationship
                $table->morphs('letterable');
                
                // Reference & Tracking
                $table->string('letter_number')->unique();
                $table->string('secure_code')->unique();
                $table->string('title')->nullable();
                $table->longText('content');
                $table->string('type')->default('approval');
                
                // Sender & Recipient
                $table->foreignId('sender_id')->nullable()->constrained('users');
                $table->foreignId('recipient_id')->constrained('users');
                
                // Reference Fields
                $table->string('your_ref')->nullable();
                $table->string('our_ref_format_id')->nullable();
                
                // Metadata
                $table->string('generated_by')->nullable();
                $table->string('generated_by_role')->nullable();
                $table->json('signatories')->nullable();
                $table->json('cc_recipients')->nullable();
                
                // Status & Tracking
                $table->string('stage')->default('draft');
                $table->boolean('is_printed')->default(false);
                $table->integer('print_count')->default(0);
                $table->string('pdf_path')->nullable();
                $table->integer('pdf_size')->nullable();
                
                // Verification
                $table->timestamp('verified_at')->nullable();
                $table->string('verified_by')->nullable();
                $table->ipAddress('verification_ip')->nullable();
                
                // File Management
                $table->string('file_number')->nullable();
                $table->string('physical_location')->nullable();
                
                // Additional
                $table->json('metadata')->nullable();
                
                $table->timestamps();
                
                // Additional indexes
                $table->index('letter_number');
                $table->index('secure_code');
                $table->index('type');
                $table->index('stage');
                $table->index('recipient_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('letters');
    }
};