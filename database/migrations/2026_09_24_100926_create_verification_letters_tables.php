<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Letters that confirm to an outside party (an employer, DataFlow, a police
 * force, a foreign ministry...) that a graduate's certificate and/or
 * transcript is authentic - see the sample letters in
 * Desktop/KUSEKWA/VERIFICATION. One letter can cover several graduates, so
 * the graduates live in their own table; the person who signs is chosen from
 * a small catalog (DVCA, DUS...) because the letterhead's office name, the
 * contact line and the signature block all follow the signatory.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_signatories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('university_id')->index();
            $table->string('name');
            $table->string('title_en');
            $table->string('unit_en')->nullable();
            $table->string('title_sw');
            $table->string('unit_sw')->nullable();
            $table->string('office_en');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('verification_letters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('university_id')->index();
            $table->string('template', 20)->default('standard');
            $table->string('language', 2)->default('en');
            $table->string('letter_number')->unique();
            $table->string('secure_code')->unique();
            $table->date('letter_date');

            $table->string('recipient_title')->nullable();
            $table->string('recipient_organisation')->nullable();
            $table->text('recipient_address')->nullable();
            $table->string('recipient_city')->nullable();
            $table->string('recipient_country')->nullable();
            $table->string('recipient_email')->nullable();
            $table->text('ufs')->nullable();

            $table->string('received_reference')->nullable();
            $table->date('received_date')->nullable();
            $table->string('received_via', 20)->default('letter');
            $table->json('documents');
            $table->string('number_type', 20)->default('certificate');
            $table->string('subject')->nullable();
            $table->string('mode_of_study')->nullable();

            $table->unsignedBigInteger('signatory_id');
            $table->unsignedBigInteger('generated_by');
            $table->timestamp('verified_at')->nullable();
            $table->string('verification_ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('verification_letter_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('verification_letter_id')->index();
            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->unsignedSmallInteger('position')->default(1);
            $table->string('name');
            $table->string('gender', 1)->nullable();
            $table->string('number');
            $table->string('programme');
            $table->string('graduation_year', 10);
            $table->string('barcode')->nullable();
            $table->string('study_from', 20)->nullable();
            $table->string('study_to', 20)->nullable();
            $table->unsignedTinyInteger('duration_years')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_letter_items');
        Schema::dropIfExists('verification_letters');
        Schema::dropIfExists('letter_signatories');
    }
};
