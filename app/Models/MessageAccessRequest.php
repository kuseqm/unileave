<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MessageAccessRequest extends Model
{
    use HasFactory;

    protected $table = 'message_access_requests';

    protected $fillable = [
        'message_id',
        'requested_by',
        'approved_by',
        'reason',
        'status',
        'one_time_code',
        'code_expires_at',
        'viewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'code_expires_at' => 'datetime',
        'viewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the message that was requested
     */
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Get the user who requested access
     */
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the user who approved the request (VC)
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if the request is expired
     */
    public function isExpired()
    {
        return $this->code_expires_at && $this->code_expires_at->isPast();
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}