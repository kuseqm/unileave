<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageAuditLog extends Model
{
    use HasFactory;

    protected $table = 'message_audit_logs';

    protected $fillable = [
        'message_id',
        'user_id',
        'action',
        'access_method',
        'one_time_code_used',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the message that this audit log belongs to
     */
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
