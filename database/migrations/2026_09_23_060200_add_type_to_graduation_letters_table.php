<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Generalizes graduation_letters beyond just the confirmation letter -
 * same table/model kept (not renamed to something broader like
 * AlumniLetter) to avoid unnecessary churn on a feature that shipped
 * today, but it now also carries General Introduction, English
 * Proficiency, and General Recommendation letters, all identified from
 * real SJUT samples - see [[project_gmsa_graduation_verification_slice]].
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('graduation_letters', function (Blueprint $table) {
            $table->string('type')->default('confirmation')->after('student_id');

            // Introduction/Recommendation letters go to a named external
            // party (e.g. "EXECUTIVE SECRETARY, CCM"), not the student -
            // x-letter.recipient already supports this via its
            // customAddress prop, this just persists what was used.
            $table->string('recipient_name')->nullable()->after('type');
            $table->text('recipient_address')->nullable()->after('recipient_name');

            // "RE: INTRODUCING MR. DACHI, HASSAN" - some types have no
            // subject line at all (English Proficiency's "TO WHOM IT MAY
            // CONCERN" carries no separate RE: line), so this stays
            // nullable rather than always-required.
            $table->string('subject')->nullable()->after('recipient_address');

            // The real letters show different offices/signatories issuing
            // different types (DVCA vs a Dean from "Directorate of
            // Undergraduate Studies") - kept on the letter row so a later
            // change to who signs doesn't alter the history of what was
            // actually printed.
            $table->string('office_title')->nullable()->after('subject');
            $table->string('signatory_title')->nullable()->after('office_title');
        });
    }

    public function down(): void
    {
        Schema::table('graduation_letters', function (Blueprint $table) {
            $table->dropColumn(['type', 'recipient_name', 'recipient_address', 'subject', 'office_title', 'signatory_title']);
        });
    }
};
