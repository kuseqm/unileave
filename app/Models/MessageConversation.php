<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageConversation extends Model
{
    use HasFactory;

    protected $table = 'message_conversations';

    protected $fillable = [
        'leave_request_id',
        'initiator_id',
        'participant_ids',
        'conversation_number',
        'subject',
        'last_message_at',
        'is_active',
        'status',
        'closed_by',
        'closed_at',
    ];

    protected $casts = [
        'participant_ids' => 'array',
        'last_message_at' => 'datetime',
        'is_active' => 'boolean',
        'closed_at' => 'datetime',
    ];

    // Relationships
    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class, 'leave_request_id');
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'conversation_id')->latest();
    }

    public function participants()
    {
        return User::whereIn('id', $this->participant_ids)->get();
    }

    // Helper methods
    public function unreadCountForUser($userId)
    {
        return $this->messages()
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function generateConversationNumber()
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->max('id') ?? 0;
        $number = str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        return "CONV/{$year}/{$number}";
    }

    public function getLastMessageText()
    {
        $last = $this->lastMessage;
        if (! $last) {
            return 'No messages';
        }

        return strlen($last->message) > 50
            ? substr($last->message, 0, 50).'...'
            : $last->message;
    }
}
