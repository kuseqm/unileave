<?php
// app/Models/LeaveAttachment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveAttachment extends Model
{
    use HasFactory;
    
    protected $table = 'leave_attachments';
    
    protected $fillable = [
        'leave_request_id', 'file_name', 'file_path', 'file_type', 'mime_type', 'size_bytes'
    ];
    
    // Relationships
    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }
    
    // Accessors
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size_bytes;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}