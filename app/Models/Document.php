<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';

    protected $fillable = [
        'user_id',
        'leave_request_id',
        'category_id',
        'original_name',
        'stored_name',
        'file_path',
        'file_size',
        'mime_type',
        'file_type',
        'description',
        'uploaded_by',
        'verified_by',
        'verified_at',
        'verification_status',
        'rejection_reason',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'file_size' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class, 'leave_request_id');
    }

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }
}
