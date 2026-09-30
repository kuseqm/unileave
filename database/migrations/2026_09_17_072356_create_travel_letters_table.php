<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('travel_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_permission_id')->constrained()->onDelete('cascade');

            // Reference & Tracking
            $table->string('letter_number')->unique()->nullable();
            $table->string('secure_code')->unique()->nullable();

            // Letter Content
            $table->string('title')->nullable();
            $table->longText('content');
            $table->string('type')->default('application');

            // Metadata & Signatures
            $table->string('generated_by')->nullable();
            $table->string('generated_by_role')->nullable();
            $table->json('signatories')->nullable();
            $table->json('cc_recipients')->nullable();

            // Status & Tracking
            $table->string('stage')->default('draft');
            $table->boolean('is_printed')->default(false);
            $table->timestamp('printed_at')->nullable();
            $table->integer('print_count')->default(0);

            // Storage
            $table->string('signature_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->integer('pdf_size')->nullable();

            // Verification
            $table->timestamp('verified_at')->nullable();
            $table->string('verified_by')->nullable();
            $table->ipAddress('verification_ip')->nullable();

            // Additional
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['travel_permission_id', 'type']);
            $table->index('letter_number');
            $table->index('secure_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_letters');
    }
};
