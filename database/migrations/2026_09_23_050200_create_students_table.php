<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * First slice of GMSA's graduation-verification feature, built here
 * instead of a separate Filament project - see the "SUPERSEDED" notice at
 * the top of gmsa's own CLAUDE.md. Deliberately lean: exact columns beyond
 * these core ones are expected to change once the user shares a real
 * sample import file - `raw_data` exists so an early import never loses a
 * column we haven't explicitly mapped yet.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            // Explicit, not relying on the server default - see the
            // MyISAM/no-FK debt finding earlier this session
            // ([[project_sms_settings_fragmentation_and_db_engine]]): every
            // table here IS already InnoDB in practice, but a stale comment
            // elsewhere in this codebase claiming otherwise is exactly the
            // kind of drift this table should never be ambiguous about.
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();

            // GMSA's own identifier philosophy applies here too: SIMS
            // registration numbers come in several existing formats
            // (e.g. `2026/0001`, `D/2026/0001`) - stored exactly as
            // received, no format validation, matched by exact value.
            $table->string('registration_number');
            $table->string('full_name');
            // Faculty/Department already exist in sms_demo as `schools`
            // (=Faculty) and `departments`, with real data (28/47 rows) -
            // Program is the one genuinely missing layer, added by the
            // companion `create_programs_table` migration this depends on.
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();

            $table->boolean('is_graduated')->default(false);
            $table->date('graduation_date')->nullable();
            $table->string('academic_year')->nullable();

            // Anything imported that doesn't map to a named column above -
            // keeps the import lossless while the real schema is still
            // being confirmed against a sample file.
            $table->json('raw_data')->nullable();

            // The `User` model maps to the `staff` table in this app, not a
            // table literally named `users` - constrained('users') looks
            // valid but silently points at an unrelated, empty scaffold
            // table, and every insert then fails its FK check.
            $table->foreignId('imported_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();

            $table->unique(['university_id', 'registration_number']);
            $table->index('full_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
