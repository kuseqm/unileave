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
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'is_verified',
        'verified_at',
        'verified_by'
    ];
    
    protected $casts = [
        'is_verified' => 'boolean',
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