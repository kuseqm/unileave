<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Persistent audit trail for every Certificate Number upload, mirroring
 * student_roster_imports' shape (same reference-number scheme, same
 * downloaded_at/uploaded_at/closed_at lifecycle) so both upload features
 * behave uniformly - kept as its own table rather than reusing
 * student_roster_imports because a certificate upload never creates a
 * graduate, only ever updates an existing one, so "created" concepts
 * don't apply here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_number_uploads', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('reference_number')->unique();
            $table->string('status')->default('open');
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->foreignId('imported_by')->constrained('staff')->cascadeOnDelete();
            $table->string('original_filename');

            $table->unsignedInteger('updated_count')->default(0);
            $table->unsignedInteger('skipped_count')->default(0);
            $table->unsignedInteger('not_found_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->json('updated_reg_numbers')->nullable();
            $table->json('flagged')->nullable();

            $table->timestamp('downloaded_at')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('staff')->nullOnDelete();

            $table->timestamps();

            $table->index(['university_id', 'graduation_year'], 'cnu_uni_year_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_number_uploads');
    }
};
