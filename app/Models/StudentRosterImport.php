<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentRosterImport extends Model
{
    protected $fillable = [
        'reference_number',
        'status',
        'university_id',
        'campus_id',
        'program_id',
        'original_program_id',
        'graduation_year',
        'imported_by',
        'original_filename',
        'created_count',
        'updated_count',
        'created_reg_numbers',
        'updated_reg_numbers',
        'flagged',
        'total_rows',
        'processed_rows',
        'downloaded_at',
        'uploaded_at',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'created_reg_numbers' => 'array',
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

    public function originalProgram()
    {
        return $this->belongsTo(Program::class, 'original_program_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'roster_import_id');
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

    public function getIsIncompleteAttribute(): bool
    {
        return $this->total_rows !== null && $this->processed_rows < $this->total_rows;
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
