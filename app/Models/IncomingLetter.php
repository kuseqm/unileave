<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncomingLetter extends Model
{
    use HasFactory;

    protected $table = 'incoming_letters';

    protected $fillable = [
        'ref_number',
        'date_received',
        'sender',
        'office_id',
        'subject',
        'pdf_path',
        'assigned_to',
        'status',
        'priority',
        'remarks',
        'created_by',
        'closed_at',
    ];

    protected $casts = [
        'date_received' => 'date',
        'closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Office this letter is addressed to
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    /**
     * Staff member assigned to handle this letter
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Staff member who created this record
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Outgoing letters sent in response to this incoming letter
     */
    public function outgoingLetters()
    {
        return $this->hasMany(OutgoingLetter::class, 'incoming_letter_id');
    }

    // ========== SCOPES ==========

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    public function scopeConfidential($query)
    {
        return $query->where('priority', 'confidential');
    }

    public function scopeByOffice($query, $officeId)
    {
        return $query->where('office_id', $officeId);
    }

    public function scopeByAssignedTo($query, $staffId)
    {
        return $query->where('assigned_to', $staffId);
    }

    // ========== ACCESSORS ==========

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'received' => 'bg-yellow',
            'assigned' => 'bg-blue',
            'in_progress' => 'bg-cyan',
            'replied' => 'bg-purple',
            'closed' => 'bg-green',
            default => 'bg-secondary',
        };
    }

    /**
     * Get priority badge class for UI
     */
    public function getPriorityBadgeClassAttribute()
    {
        return match($this->priority) {
            'urgent' => 'bg-red',
            'confidential' => 'bg-dark',
            default => 'bg-secondary',
        };
    }

    /**
     * Get days since received
     */
    public function getDaysPendingAttribute()
    {
        if ($this->status === 'closed') {
            return null;
        }
        return $this->date_received->diffInDays(now());
    }

    // ========== HELPERS ==========

    /**
     * Check if letter is overdue (more than 5 days without reply)
     */
    public function isOverdue()
    {
        if ($this->status === 'closed' || $this->status === 'replied') {
            return false;
        }
        return $this->days_pending > 5;
    }
}