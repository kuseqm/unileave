<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Append-only record of every /verify/{code} scan, successful or not -
 * see the migration's own comment for the full reasoning. The only update
 * a row ever gets is the `is_archived` toggle (see archive()/unarchive()
 * below) - no updated_at column exists, since that one flag doesn't need
 * its own timestamp.
 */
class DocumentVerificationLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'document_type',
        'document_id',
        'university_id',
        'code',
        'successful',
        'ip_address',
        'user_agent',
        'is_archived',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'is_archived' => 'boolean',
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    /**
     * How many times a specific document has been scanned - the exact
     * question this table exists to answer, that LeaveLetter/TravelLetter's
     * own verified_at/verification_ip (a single overwritten row) never
     * could.
     */
    public static function countFor(string $documentType, int $documentId): int
    {
        return static::query()
            ->where('document_type', $documentType)
            ->where('document_id', $documentId)
            ->where('successful', true)
            ->count();
    }
}
