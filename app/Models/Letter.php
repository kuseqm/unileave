<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Letter extends Model
{
    use HasFactory;

    protected $table = 'letters';

    protected $fillable = [
        // Polymorphic
        'letterable_type',
        'letterable_id',
        
        // Reference & Tracking
        'letter_number',
        'secure_code',
        'title',
        'content',
        'raw_content',
        'auto_number_paragraphs',
        'type',
        
        // Sender & Recipient
        'sender_id',
        'recipient_id',
        
        // Reference Fields
        'your_ref',
        'our_ref_format_id',
        
        // Metadata
        'generated_by',
        'generated_by_role',
        'signatories',
        'cc_recipients',
        
        // Status & Tracking
        'stage',
        'is_printed',
        'print_count',
        'pdf_path',
        'pdf_size',
        'submitted_at',
        
        // Verification
        'verified_at',
        'verified_by',
        'verification_ip',
        
        // File Management
        'file_number',
        'physical_location',
        
        // HOD Approval
        'hod_approved_at',
        'hod_approved_by',
        'hod_ip_address',
        'hod_user_agent',
        
        // Dean Approval
        'dean_approved_at',
        'dean_approved_by',
        'dean_ip_address',
        'dean_user_agent',
        
        // DVC Approval
        'dvc_approved_at',
        'dvc_approved_by',
        'dvc_ip_address',
        'dvc_user_agent',
        
        // VC Signature
        'vc_signed_at',
        'vc_signed_by',
        'vc_ip_address',
        'vc_user_agent',
        
        // Document Security
        'document_hash',
        'is_revoked',
        'revoked_at',
        'revoked_by',
        'revoked_reason',
        
        // Signature Info
        'signatory',
        'signatory_title',
        'approved_at',
        'approved_by',
        'approver_role',
        'signed_at',
        'signed_by',
        
        // Approval Comments
        'approval_comments',
        
        // Additional
        'metadata',
    ];

    protected $casts = [
        'signatories' => 'array',
        'cc_recipients' => 'array',
        'metadata' => 'array',
        'approval_comments' => 'array',
        'is_printed' => 'boolean',
        'is_revoked' => 'boolean',
        'auto_number_paragraphs' => 'boolean',
        'verified_at' => 'datetime',
        'submitted_at' => 'datetime',
        'hod_approved_at' => 'datetime',
        'dean_approved_at' => 'datetime',
        'dvc_approved_at' => 'datetime',
        'vc_signed_at' => 'datetime',
        'approved_at' => 'datetime',
        'signed_at' => 'datetime',
        'revoked_at' => 'datetime',
        'print_count' => 'integer',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Polymorphic relationship - Get the parent model
     */
    public function letterable()
    {
        return $this->morphTo();
    }

    /**
     * Sender of the letter (staff or system user)
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Recipient of the letter (staff member)
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * HOD who approved
     */
    public function hodApprover()
    {
        return $this->belongsTo(User::class, 'hod_approved_by');
    }

    /**
     * Dean who approved
     */
    public function deanApprover()
    {
        return $this->belongsTo(User::class, 'dean_approved_by');
    }

    /**
     * DVC who approved
     */
    public function dvcApprover()
    {
        return $this->belongsTo(User::class, 'dvc_approved_by');
    }

    /**
     * VC who signed
     */
    public function vcSigner()
    {
        return $this->belongsTo(User::class, 'vc_signed_by');
    }

    // ============================================
    // ACCESSORS
    // ============================================

    /**
     * Get formatted letter number
     */
    public function getFormattedLetterNumberAttribute()
    {
        return $this->letter_number ?? 'N/A';
    }

    /**
     * Get verification URL for QR code
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
     * Get letter type label
     */
    public function getTypeLabelAttribute()
    {
        $labels = [
            'general' => 'General Correspondence',
            'application' => 'Job Application',
            'request' => 'Request Letter',
            'approval' => 'Approval Letter',
            'rejection' => 'Rejection Letter',
            'notification' => 'Notification',
            'circular' => 'Circular/Memo',
            'recall' => 'Recall Letter',
            'amendment' => 'Amendment Letter',
            'offer' => 'Offer Letter',
            'contract' => 'Contract Letter',
        ];
        
        return $labels[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Get stage label
     */
    public function getStageLabelAttribute()
    {
        $labels = [
            'draft' => 'Draft',
            'pending_approval' => 'Pending Approval',
            'approved' => 'Approved',
            'signed' => 'Signed',
            'printed' => 'Printed',
            'archived' => 'Archived',
            'rejected' => 'Rejected',
            'revoked' => 'Revoked',
        ];
        
        return $labels[$this->stage] ?? ucfirst($this->stage);
    }

    /**
     * Get current approval level
     */
    public function getCurrentApprovalLevelAttribute()
    {
        if (!$this->hod_approved_at) return 'hod';
        if (!$this->dean_approved_at) return 'dean';
        if (!$this->dvc_approved_at) return 'dvc';
        if (!$this->vc_signed_at) return 'vc';
        return 'completed';
    }

    /**
     * Get next approver role name
     */
    public function getNextApproverRoleAttribute()
    {
        $level = $this->current_approval_level;
        $roles = [
            'hod' => 'Head of Department',
            'dean' => 'Dean of Faculty',
            'dvc' => 'Deputy Vice Chancellor',
            'vc' => 'Vice Chancellor',
            'completed' => 'Completed',
        ];
        return $roles[$level] ?? 'Unknown';
    }

    /**
     * Get approval progress percentage
     */
    public function getApprovalProgressAttribute()
    {
        $progress = 0;
        if ($this->hod_approved_at) $progress += 25;
        if ($this->dean_approved_at) $progress += 25;
        if ($this->dvc_approved_at) $progress += 25;
        if ($this->vc_signed_at) $progress += 25;
        return $progress;
    }

    /**
     * Check if letter has been tampered
     */
    public function getIsTamperedAttribute()
    {
        if (!$this->document_hash || !$this->vc_signed_at) {
            return false;
        }
        
        $currentHash = hash('sha256', 
            $this->content . 
            $this->letter_number . 
            $this->vc_signed_at->toDateTimeString()
        );
        
        return $currentHash !== $this->document_hash;
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope for approval letters
     */
    public function scopeApproval($query)
    {
        return $query->where('type', 'approval');
    }

    /**
     * Scope for rejection letters
     */
    public function scopeRejection($query)
    {
        return $query->where('type', 'rejection');
    }

    /**
     * Scope for draft letters
     */
    public function scopeDraft($query)
    {
        return $query->where('stage', 'draft');
    }

    /**
     * Scope for pending approval letters
     */
    public function scopePendingApproval($query)
    {
        return $query->where('stage', 'pending_approval');
    }

    /**
     * Scope for signed letters
     */
    public function scopeSigned($query)
    {
        return $query->where('stage', 'signed');
    }

    /**
     * Scope for letters by recipient
     */
    public function scopeForRecipient($query, $userId)
    {
        return $query->where('recipient_id', $userId);
    }

    /**
     * Scope for letters by module type
     */
    public function scopeForModule($query, $moduleType)
    {
        return $query->where('letterable_type', $moduleType);
    }

    /**
     * Scope for letters pending HOD approval
     */
    public function scopePendingHodApproval($query)
    {
        return $query->where('stage', 'pending_approval')
            ->whereNull('hod_approved_at');
    }

    /**
     * Scope for letters pending Dean approval
     */
    public function scopePendingDeanApproval($query)
    {
        return $query->where('stage', 'pending_approval')
            ->whereNotNull('hod_approved_at')
            ->whereNull('dean_approved_at');
    }

    /**
     * Scope for letters pending DVC approval
     */
    public function scopePendingDvcApproval($query)
    {
        return $query->where('stage', 'pending_approval')
            ->whereNotNull('hod_approved_at')
            ->whereNotNull('dean_approved_at')
            ->whereNull('dvc_approved_at');
    }

    /**
     * Scope for letters pending VC signature
     */
    public function scopePendingVcSignature($query)
    {
        return $query->where('stage', 'approved')
            ->whereNull('vc_signed_at');
    }

    // ============================================
    // METHODS
    // ============================================

    /**
     * Mark letter as printed
     */
    public function markAsPrinted()
    {
        $this->update([
            'is_printed' => true,
            'printed_at' => now(),
            'print_count' => $this->print_count + 1,
            'stage' => 'printed',
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
            'verification_count' => ($this->verification_count ?? 0) + 1,
        ]);
    }

    /**
     * Generate document hash after signing
     */
    public function generateDocumentHash()
    {
        $hash = hash('sha256', 
            $this->content . 
            $this->letter_number . 
            ($this->vc_signed_at ?? now())->toDateTimeString()
        );
        
        $this->update(['document_hash' => $hash]);
        return $hash;
    }

    /**
     * Check if user can approve this letter at current level
     */
    public function canUserApprove($user)
    {
        $role = $user->getRoleNames()->first();
        $currentLevel = $this->current_approval_level;
        
        switch ($currentLevel) {
            case 'hod':
                return $role === 'hod' && $user->department_id === $this->recipient->department_id;
            case 'dean':
                return $role === 'dean';
            case 'dvc':
                $expectedRole = $this->recipient->staff_category === 'academic' ? 'dvca' : 'dvc-pfa';
                return $role === $expectedRole;
            case 'vc':
                return $role === 'vice-chancellor';
            default:
                return false;
        }
    }

    /**
     * Get approval timeline as array
     */
    public function getApprovalTimelineAttribute()
    {
        $timeline = [];
        
        if ($this->hod_approved_at) {
            $timeline[] = [
                'level' => 'HOD',
                'approved_at' => $this->hod_approved_at,
                'approved_by' => $this->hodApprover->full_name ?? 'Unknown',
                'ip' => $this->hod_ip_address,
            ];
        }
        
        if ($this->dean_approved_at) {
            $timeline[] = [
                'level' => 'Dean',
                'approved_at' => $this->dean_approved_at,
                'approved_by' => $this->deanApprover->full_name ?? 'Unknown',
                'ip' => $this->dean_ip_address,
            ];
        }
        
        if ($this->dvc_approved_at) {
            $timeline[] = [
                'level' => 'DVC',
                'approved_at' => $this->dvc_approved_at,
                'approved_by' => $this->dvcApprover->full_name ?? 'Unknown',
                'ip' => $this->dvc_ip_address,
            ];
        }
        
        if ($this->vc_signed_at) {
            $timeline[] = [
                'level' => 'Vice Chancellor',
                'approved_at' => $this->vc_signed_at,
                'approved_by' => $this->vcSigner->full_name ?? 'Unknown',
                'ip' => $this->vc_ip_address,
            ];
        }
        
        return $timeline;
    }

    /**
     * Generate secure code if not exists
     */
    public static function generateSecureCode()
    {
        $random = strtoupper(substr(md5(uniqid()), 0, 8));
        $serial = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $random . $serial;
    }
}