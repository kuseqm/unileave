<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{
    protected $table = 'import_history';

    protected $fillable = [
        'user_id',
        'role_code',
        'sequence_number',
        'operation_type',
        'original_filename',
        'stored_filename',
        'file_size',
        'total_rows',
        'successful_rows',
        'failed_rows',
        'errors',
        'status',
        'is_test_mode',
        'error_report_path',
        'processing_started_at',
        'processing_completed_at',
        // ✅ Added location columns
        'university_id',
        'campus_id',
        'school_id',
        'department_id',
    ];

    protected $casts = [
        'errors' => 'array',
        'is_test_mode' => 'boolean',
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ✅ Optional: Add relationships for easier access
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
