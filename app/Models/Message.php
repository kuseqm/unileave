<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $table = 'messages';

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'receiver_id',
        'leave_request_id',
        'message_number',
        'subject',
        'message',
        'message_type',
        'recipient_ids',
        'priority',
        'status',
        'is_read',
        'read_at',
        'delivered_at',
        'replied_at',
        'deadline',
        'is_archived',
        'is_starred',
        'attachments',
        'parent_message_id',
        'related_incoming_mail_id',
        'related_outgoing_mail_id',
        'related_file_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_archived' => 'boolean',
        'is_starred' => 'boolean',
        'read_at' => 'datetime',
        'delivered_at' => 'datetime',
        'replied_at' => 'datetime',
        'deadline' => 'datetime',
        'attachments' => 'array',
        'recipient_ids' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function conversation()
    {
        return $this->belongsTo(MessageConversation::class, 'conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class, 'leave_request_id');
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_message_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_message_id');
    }

    /**
     * Get attachments for this message
     */
    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class, 'message_id');
    }

    public function actions()
    {
        return $this->hasMany(MessageAction::class, 'message_id');
    }

    /**
     * Get access requests for this message
     */
    public function accessRequests()
    {
        return $this->hasMany(MessageAccessRequest::class, 'message_id');
    }

    /**
     * Get audit logs for this message
     */
    public function auditLogs()
    {
        return $this->hasMany(MessageAuditLog::class, 'message_id');
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeUnread($query, $userId = null)
    {
        $query = $query->where('is_read', false);
        if ($userId) {
            $query->where('receiver_id', $userId);
        }

        return $query;
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('sender_id', $userId)->orWhere('receiver_id', $userId);
    }

    public function scopeInbox($query, $userId)
    {
        return $query->where('receiver_id', $userId)
            ->where('status', '!=', 'draft')
            ->where('is_archived', false)
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
            ->orderBy('created_at', 'desc');
    }

    public function scopeSent($query, $userId)
    {
        return $query->where('sender_id', $userId)
            ->where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope for users who can see messages based on their role
     */
    public function scopeVisibleTo($query, $user)
    {
        $role = $user->getRoleNames()->first();

        switch ($role) {
            case 'admin':
            case 'hr_manager':
                return $query; // See all messages

            case 'school_admin':
                // See messages from their school only
                return $query->whereHas('sender.department.school', function ($q) use ($user) {
                    $q->where('id', $user->school_id);
                })->orWhereHas('receiver.department.school', function ($q) use ($user) {
                    $q->where('id', $user->school_id);
                });

            case 'hod':
                // See messages from their department only
                return $query->whereHas('sender.department', function ($q) use ($user) {
                    $q->where('id', $user->department_id);
                })->orWhereHas('receiver.department', function ($q) use ($user) {
                    $q->where('id', $user->department_id);
                });

            default:
                return $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
        }
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    public function markAsRead()
    {
        if (! $this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
                'status' => 'read',
            ]);

            $this->actions()->create([
                'user_id' => auth()->id(),
                'action' => 'opened',
            ]);
        }
    }

    public function markAsReplied()
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now(),
        ]);
    }

    public function generateMessageNumber()
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->max('id') ?? 0;
        $number = str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        return "MSG/{$year}/{$number}";
    }

    /**
     * Soft delete the message (kept for 7 years retention)
     */
    public function softDelete()
    {
        $this->delete(); // Uses SoftDeletes trait
    }

    /**
     * Restore a soft-deleted message
     */
    public function restoreMessage()
    {
        $this->restore();
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getPriorityBadgeAttribute()
    {
        return match ($this->priority) {
            'urgent' => '<span class="badge bg-danger">Urgent</span>',
            'high' => '<span class="badge bg-warning">High</span>',
            'low' => '<span class="badge bg-secondary">Low</span>',
            default => '<span class="badge bg-info">Normal</span>',
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'sent' => '<span class="badge bg-info">Sent</span>',
            'delivered' => '<span class="badge bg-primary">Delivered</span>',
            'read' => '<span class="badge bg-success">Read</span>',
            'replied' => '<span class="badge bg-success">Replied</span>',
            'actioned' => '<span class="badge bg-dark">Actioned</span>',
            default => '<span class="badge bg-secondary">'.$this->status.'</span>',
        };
    }

    /**
     * Get attachment count safely (returns 0 if null)
     */
    public function getAttachmentCountAttribute()
    {
        return $this->attachments ? $this->attachments->count() : 0;
    }

    /**
     * Check if message has attachments
     */
    public function getHasAttachmentsAttribute()
    {
        return $this->attachments && $this->attachments->count() > 0;
    }

    /**
     * Get all recipients (for multiple recipient messages)
     */
    public function recipients()
    {
        if ($this->recipient_ids && is_array($this->recipient_ids)) {
            return User::whereIn('id', $this->recipient_ids)->get();
        }

        if ($this->receiver_id) {
            return collect([$this->receiver]);
        }

        return collect();
    }

    /**
     * Get recipient names as string
     */
    public function getRecipientNamesAttribute()
    {
        $names = [];

        if ($this->recipient_ids && is_array($this->recipient_ids)) {
            $recipients = User::whereIn('id', $this->recipient_ids)->get();
            foreach ($recipients as $recipient) {
                $names[] = $recipient->first_name.' '.$recipient->last_name;
            }

            return implode(', ', $names);
        }

        if ($this->receiver) {
            return $this->receiver->first_name.' '.$this->receiver->last_name;
        }

        return 'Unknown';
    }

    /**
     * Check if message is soft deleted
     */
    public function getIsDeletedAttribute()
    {
        return $this->trashed();
    }

    /**
     * Get deletion info for audit
     */
    public function getDeletionInfoAttribute()
    {
        if ($this->trashed()) {
            return [
                'deleted_at' => $this->deleted_at,
                'deleted_by' => 'user_id_'.($this->deleted_by ?? 'unknown'),
            ];
        }

        return null;
    }

    /**
     * Check if a user can view the content of this message
     * Only sender, receiver, or VC-approved access can view content
     */
    public function canViewContent($user)
    {
        // Sender can view
        if ($user->id == $this->sender_id) {
            return true;
        }

        // Receiver can view
        if ($user->id == $this->receiver_id) {
            return true;
        }

        // Check if user has an approved one-time code for this message
        $accessRequest = MessageAccessRequest::where('message_id', $this->id)
            ->where('requested_by', $user->id)
            ->where('status', 'approved')
            ->where('code_expires_at', '>', now())
            ->first();

        if ($accessRequest) {
            return true;
        }

        // Admin and VC can view for compliance
        if ($user->hasRole('admin') || $user->hasRole('vice-chancellor')) {
            return true;
        }

        return false;
    }
}
