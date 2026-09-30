<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateNumberUpload extends Model
{
    protected $fillable = [
        'reference_number',
        'status',
        'university_id',
        'campus_id',
        'program_id',
        'graduation_year',
        'imported_by',
        'original_filename',
        'updated_count',
        'skipped_count',
        'not_found_count',
        'failed_count',
        'updated_reg_numbers',
        'flagged',
        'downloaded_at',
        'uploaded_at',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'updated_reg_numbers' => 'array',
        'flagged' => 'array',
        'downloaded_at' => 'datetime',
        'uploaded_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function importedBy()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function getFlaggedCountAttribute(): int
    {
        return count($this->flagged ?? []);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
