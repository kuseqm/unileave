<?php

// app/Models/ApprovalLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    use HasFactory;

    protected $table = 'approval_logs';

    protected $fillable = [
        'request_type', 'request_id', 'from_role', 'to_role',
        'action', 'comment', 'approver_staff_id', 'ip_address',
    ];

    // Relationships
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_staff_id');
    }

    // ✅ ADD THIS - Direct relationship for leave requests
    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class, 'request_id');
    }

    // Keep for other types if needed
    public function request()
    {
        return $this->belongsTo(LeaveRequest::class, 'request_id');
    }

    // Scopes
    public function scopeLeaveRequests($query)
    {
        return $query->where('request_type', 'leave');
    }

    public function scopePermissionRequests($query)
    {
        return $query->where('request_type', 'permission');
    }

    public function scopeForRequest($query, $type, $id)
    {
        return $query->where('request_type', $type)->where('request_id', $id);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}
