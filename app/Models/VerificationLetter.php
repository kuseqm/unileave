<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationLetter extends Model
{
    public const TEMPLATES = ['standard' => 'Standard', 'dataflow' => 'DataFlow', 'custom' => 'Write the whole letter myself'];

    public const LANGUAGES = ['en' => 'English', 'sw' => 'Swahili'];

    public const RECEIVED_VIA = [
        'letter' => 'A letter',
        'email' => 'An email (with a letter attached)',
        'graduate' => 'A request from the graduate',
    ];

    public const NUMBER_TYPES = ['certificate' => 'Certificate number', 'registration' => 'Registration number'];

    protected $fillable = [
        'university_id', 'template', 'language', 'letter_number', 'secure_code', 'letter_date',
        'recipient_title', 'recipient_organisation', 'recipient_address', 'recipient_city',
        'recipient_country', 'recipient_email', 'ufs', 'received_reference', 'received_date',
        'received_via', 'documents', 'number_type', 'subject', 'mode_of_study', 'extra_text', 'body_text',
        'signatory_id', 'generated_by', 'verified_at', 'verification_ip',
    ];

    protected $casts = [
        'letter_date' => 'date',
        'received_date' => 'date',
        'documents' => 'array',
        'verified_at' => 'datetime',
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function signatory()
    {
        return $this->belongsTo(LetterSignatory::class, 'signatory_id');
    }

    public function items()
    {
        return $this->hasMany(VerificationLetterItem::class)->orderBy('position');
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function markAsVerified(?string $ip): void
    {
        $this->forceFill(['verified_at' => $this->verified_at ?? now(), 'verification_ip' => $ip])->save();
    }
}
