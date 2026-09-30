<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        // 'Bachelor' | 'Master' | 'PhD' | 'Diploma' | 'Certificate' - drives
        // phrasing like "a distinguished Masters student" on alumni
        // letters, instead of parsing it out of the programme name.
        'study_level',
        // Normal length in years - lets us estimate a student's start year
        // from their graduation year when the reg number carries no year.
        'duration_years',
        'code',
        'status',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * The degree title awarded, as opposed to the programme studied: a
     * subject combination such as "Bachelor of Science with Education
     * (Physics - Mathematics)" still earns a "Bachelor of Science with
     * Education". Falls back to the programme's own name when there is no
     * such base programme in the catalog.
     */
    public function getDegreeNameAttribute(): string
    {
        return $this->baseProgramme()?->name ?? $this->name;
    }

    /**
     * "BScEd" - the base programme's code. Internal registry codes such as
     * SJ00113 are not abbreviations anyone would print, so they give null.
     */
    public function getDegreeAbbreviationAttribute(): ?string
    {
        $code = ($this->baseProgramme() ?? $this)->code;

        return $code && ! preg_match('/^SJ[DM]?\d/i', $code) ? $code : null;
    }

    /** The programme without its trailing "(subject combination)", when that exists in the same university. */
    private function baseProgramme(): ?self
    {
        $baseName = trim(preg_replace('/\s*\([^)]*\)\s*$/', '', $this->name));

        if ($baseName === '' || $baseName === trim($this->name)) {
            return null;
        }

        return static::whereHas('school.campus', fn ($query) => $query->where(
            'university_id',
            $this->school?->campus?->university_id
        ))
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($baseName)])
            ->first();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
