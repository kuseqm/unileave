<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per issued graduation confirmation letter - mirrors
 * leave_letters/travel_letters (secure_code + letter_number, public
 * verification via VerifyController), except the number comes from
 * Registry's ReferenceFormat (document_type=graduation) instead of the
 * hardcoded LetterTrait generator those two use - see the "Registry as the
 * central reference-number source" decision this session.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graduation_letters', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('letter_number');
            $table->string('secure_code')->unique();
            // See students table's own migration comment: `User` maps to
            // the `staff` table here, not `users`.
            $table->foreignId('generated_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('verification_ip')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduation_letters');
    }
};
