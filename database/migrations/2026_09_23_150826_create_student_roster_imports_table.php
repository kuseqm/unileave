<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Persistent audit trail for every Graduation Roster upload - who imported
 * it, when, and what happened - so staff aren't re-explaining the same
 * batch verbally and there's a real record for accountability, matching
 * the existing staff-import ImportHistory pattern but scoped to this
 * feature rather than reusing that HR-specific table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_roster_imports', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('imported_by')->constrained('staff')->cascadeOnDelete();
            $table->string('original_filename');
            $table->unsignedInteger('created_count')->default(0);
            $table->unsignedInteger('updated_count')->default(0);
            $table->json('flagged')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_roster_imports');
    }
};
