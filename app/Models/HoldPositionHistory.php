<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoldPositionHistory extends Model
{
    use HasFactory;

    protected $table = 'hold_position_histories';

    protected $fillable = [
        'leave_request_id',
        'requester_staff_id',
        'holder_staff_id',
        'status',
        'requested_at',
        'responded_at',
        'comment',
        'token',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    // Relationships
    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class, 'leave_request_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_staff_id');
    }

    public function holder()
    {
        return $this->belongsTo(User::class, 'holder_staff_id');
    }

    // Accessor for badge color
    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'accepted':
                return 'success';
            case 'pending':
                return 'warning';
            case 'declined':
                return 'danger';
            default:
                return 'secondary';
        }
    }
}
