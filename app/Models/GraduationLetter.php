<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Despite the name (kept to avoid renaming churn on a same-day feature),
 * this now covers every single-student alumni letter type identified from
 * real SJUT samples, not just graduation confirmation - see
 * GraduationLetterController::LETTER_TYPES and
 * [[project_gmsa_graduation_verification_slice]].
 */
class GraduationLetter extends Model
{
    protected $fillable = [
        'university_id',
        'student_id',
        'type',
        'recipient_name',
        'recipient_address',
        'subject',
        'office_title',
        'signatory_title',
        'body_content',
        'letter_number',
        'secure_code',
        'generated_by',
        'verified_at',
        'verification_ip',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function markAsVerified(?string $ip): void
    {
        $this->update(['verified_at' => now(), 'verification_ip' => $ip]);
    }
}
