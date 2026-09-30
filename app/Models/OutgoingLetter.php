<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingLetter extends Model
{
    use HasFactory;

    protected $table = 'outgoing_letters';

    protected $fillable = [
        'ref_number',
        'date_sent',
        'recipient',
        'subject',
        'pdf_path',
        'dispatched_via',
        'incoming_letter_id',
        'drafted_by',
        'approved_by',
        'status',
        'approval_comments',
        'requires_print',
        'dispatched_at',
    ];

    protected $casts = [
        'date_sent' => 'date',
        'dispatched_at' => 'datetime',
        'requires_print' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Incoming letter that this outgoing letter responds to
     */
    public function incomingLetter()
    {
        return $this->belongsTo(IncomingLetter::class, 'incoming_letter_id');
    }

    /**
     * Staff member who drafted this letter
     */
    public function draftedBy()
    {
        return $this->belongsTo(User::class, 'drafted_by');
    }

    /**
     * Staff member who approved this letter
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Print queue entry for this letter (if requires_print = true)
     */
    public function printQueue()
    {
        return $this->hasOne(PrintQueue::class, 'outgoing_letter_id');
    }

    // ========== SCOPES ==========

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDispatched($query)
    {
        return $query->where('status', 'dispatched');
    }

    public function scopeRequiresPrint($query)
    {
        return $query->where('requires_print', true)->where('status', 'approved');
    }

    // ========== ACCESSORS ==========

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'draft' => 'bg-secondary',
            'pending_approval' => 'bg-warning',
            'approved' => 'bg-info',
            'rejected' => 'bg-danger',
            'dispatched' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    /**
     * Get dispatched via badge class
     */
    public function getDispatchedViaBadgeClassAttribute()
    {
        return match ($this->dispatched_via) {
            'email' => 'bg-primary',
            'post' => 'bg-warning',
            'hand' => 'bg-success',
            default => 'bg-secondary',
        };
    }
}
