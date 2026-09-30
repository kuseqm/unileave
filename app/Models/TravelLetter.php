<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelLetter extends Model
{
    use HasFactory;

    protected $table = 'travel_letters';

    protected $fillable = [
        'travel_permission_id',
        'letter_number',
        'secure_code',
        'title',
        'content',
        'type',
        'generated_by',
        'generated_by_role',
        'signature_path',
        'signatories',
        'cc_recipients',
        'stage',
        'is_printed',
        'printed_at',
        'print_count',
        'pdf_path',
        'pdf_size',
        'verified_at',
        'verified_by',
        'verification_ip',
        'metadata',
    ];

    protected $casts = [
        'signatories' => 'array',
        'cc_recipients' => 'array',
        'metadata' => 'array',
        'is_printed' => 'boolean',
        'printed_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function travelPermission()
    {
        return $this->belongsTo(TravelPermission::class);
    }

    public function getVerificationUrlAttribute()
    {
        if ($this->secure_code) {
            // verify.letter only ever looks up LeaveLetter - the unified
            // verify.show link is the one that also checks TravelLetter.
            return route('verify.show', $this->secure_code);
        }

        return null;
    }

    public function markAsPrinted()
    {
        $this->update([
            'is_printed' => true,
            'printed_at' => now(),
            'print_count' => $this->print_count + 1,
        ]);
    }

    public function markAsVerified($ip = null)
    {
        $this->update([
            'verified_at' => now(),
            'verified_by' => auth()->user()->full_name ?? 'System',
            'verification_ip' => $ip ?? request()->ip(),
        ]);
    }
}
