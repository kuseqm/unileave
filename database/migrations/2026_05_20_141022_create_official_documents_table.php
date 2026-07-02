<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('official_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique(); // e.g., LTR-2025-0001
            $table->string('type'); // leave_approval, contract, etc.
            $table->morphs('related'); // related_id + related_type (for leave_request, staff, etc.)
            $table->foreignId('generated_by')->constrained('users');
            $table->string('file_path'); // stored PDF path
            $table->string('qr_code_path')->nullable();
            $table->text('qr_code_data')->nullable(); // JSON or text encoded in QR
            $table->string('digital_signature_path')->nullable();
            $table->boolean('is_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->string('signed_by')->nullable();
            $table->text('verification_url')->nullable();
            $table->string('status')->default('draft'); // draft, signed, archived
            $table->timestamps();

            $table->index(['type', 'related_id']);
            $table->index('document_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('official_documents');
    }
};