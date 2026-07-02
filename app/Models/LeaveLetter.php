<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveLetter extends Model
{
    use HasFactory;

    protected $table = 'leave_letters';

    protected $fillable = [
        'leave_request_id',
        'staff_id',  // ✅ ADDED: Link to staff/user who requested the leave
        'letter_number',
        'secure_code',
        'title',
        'content',
        'type',
        'generated_by',
        'generated_by_role',
        'signatories',
        'cc_recipients',
        'payment_status',
        'salary_deduction',
        'financial_notes',
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
        'salary_deduction' => 'decimal:2',
    ];

    /**
     * Get the leave request that owns this letter
     */
    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    /**
     * Get the staff member who owns this letter
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the verification URL for this letter
     */
    public function getVerificationUrlAttribute()
    {
        if ($this->secure_code) {
            return route('verify.letter', $this->secure_code);
        }
        return null;
    }

    /**
     * Check if letter has been verified
     */
    public function getIsVerifiedAttribute()
    {
        return !is_null($this->verified_at);
    }

    /**
     * Check if letter is a draft
     */
    public function getIsDraftAttribute()
    {
        return $this->stage === 'draft';
    }

    /**
     * Check if letter is generated/approved
     */
    public function getIsGeneratedAttribute()
    {
        return $this->stage === 'generated';
    }

    /**
     * Get formatted letter number with prefix
     */
    public function getFormattedLetterNumberAttribute()
    {
        return $this->letter_number;
    }

    /**
     * Scope for approval letters
     */
    public function scopeApproval($query)
    {
        return $query->where('type', 'approval');
    }

    /**
     * Scope for recall letters
     */
    public function scopeRecall($query)
    {
        return $query->where('type', 'recall');
    }

    /**
     * Scope for rejection letters
     */
    public function scopeRejection($query)
    {
        return $query->where('type', 'rejection');
    }

    /**
     * Scope for generated letters (not draft)
     */
    public function scopeGenerated($query)
    {
        return $query->where('stage', 'generated');
    }

    /**
     * Scope for draft letters
     */
    public function scopeDraft($query)
    {
        return $query->where('stage', 'draft');
    }

    /**
     * Scope for letters belonging to a specific staff
     */
    public function scopeForStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    /**
     * Mark letter as printed
     */
    public function markAsPrinted()
    {
        $this->update([
            'is_printed' => true,
            'printed_at' => now(),
            'print_count' => $this->print_count + 1,
        ]);
    }

    /**
     * Mark letter as verified
     */
    public function markAsVerified($ip = null)
    {
        $this->update([
            'verified_at' => now(),
            'verified_by' => auth()->user()->full_name ?? 'System',
            'verification_ip' => $ip ?? request()->ip(),
        ]);
    }

    /**
     * Mark letter as generated (approved and ready)
     */
    public function markAsGenerated()
    {
        $this->update([
            'stage' => 'generated',
        ]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($status, $deduction = null, $notes = null)
    {
        $updateData = ['payment_status' => $status];
        
        if ($deduction !== null) {
            $updateData['salary_deduction'] = $deduction;
        }
        
        if ($notes !== null) {
            $updateData['financial_notes'] = $notes;
        }
        
        $this->update($updateData);
    }
}