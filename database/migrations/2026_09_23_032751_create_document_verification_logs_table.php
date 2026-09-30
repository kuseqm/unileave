<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per QR/code scan on the public /verify/{code} page - every
 * attempt, successful or not. Deliberately append-only (INSERT, never
 * UPDATE) - this is the fix for a real gap found while building the
 * verification page: LeaveLetter::markAsVerified()/TravelLetter's own
 * version OVERWRITE a single verified_at/verified_by/verification_ip row
 * on every scan, so the institution could never see how many times a
 * document was checked, only the most recent check. Those columns stay
 * as a "last seen" convenience on the letter itself; this table is the
 * actual count/history, queryable per document and across failed lookups
 * too (a `code` that never matched anything is worth keeping - repeated
 * failed attempts at a similar code can be a sign of someone probing for
 * a valid one).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_verification_logs', function (Blueprint $table) {
            $table->id();
            // 'leave_letter' | 'travel_letter' | 'report' | 'unknown' -
            // 'unknown' covers a code that matched nothing at all (the
            // verify/not-found.blade.php case) - document_id stays null
            // there, since there's no row to point at.
            $table->string('document_type');
            $table->unsignedBigInteger('document_id')->nullable();
            // The raw code as scanned - kept even on failure, precisely
            // so a pattern of near-miss codes (typos vs. genuine probing)
            // is visible later, not just successful lookups.
            $table->string('code');
            $table->boolean('successful');
            $table->string('ip_address')->nullable();
            // Free text, not a fixed-length column - real user-agent
            // strings vary a lot in length and this is never matched
            // against, only displayed.
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['document_type', 'document_id']);
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_verification_logs');
    }
};
