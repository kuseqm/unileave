<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_request_id')->constrained()->onDelete('cascade');
            
            // Reference & Tracking
            $table->string('letter_number')->unique()->nullable(); // Format: LTR/2024/0001
            $table->string('secure_code')->unique()->nullable();    // For QR verification: e.g., "SMU-LTR-A1B2C3D4"
            
            // Letter Content
            $table->string('title')->nullable();
            $table->longText('content');                            // Full HTML content
            $table->string('type')->default('approval');            // approval, rejection, recall, amendment
            
            // Metadata & Signatures
            $table->string('generated_by')->nullable();              // Who generated (HR Officer name)
            $table->string('generated_by_role')->nullable();        // hr-officer, dvc, etc.
            $table->json('signatories')->nullable();                // Store multiple signatories
            $table->json('cc_recipients')->nullable();              // CC: HOD, Dean, etc.
            
            // Payment & Financial Info
            $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('paid');
            $table->decimal('salary_deduction', 10, 2)->nullable();   // If unpaid
            $table->text('financial_notes')->nullable();
            
            // Status & Tracking
            $table->string('stage')->default('draft');               // draft, generated, printed, archived
            $table->boolean('is_printed')->default(false);
            $table->timestamp('printed_at')->nullable();
            $table->integer('print_count')->default(0);
            
            // Storage
            $table->string('pdf_path')->nullable();                  // Path to stored PDF
            $table->integer('pdf_size')->nullable();                 // File size in KB
            
            // Verification
            $table->timestamp('verified_at')->nullable();
            $table->string('verified_by')->nullable();
            $table->ipAddress('verification_ip')->nullable();
            
            // Additional
            $table->json('metadata')->nullable();                    // Extra data
            
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index(['leave_request_id', 'type']);
            $table->index('letter_number');
            $table->index('secure_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_letters');
    }
};