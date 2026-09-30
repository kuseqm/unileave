<?php

namespace App\Models;

use App\Helpers\DateToWordsHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGraduation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'university_id',
        'campus_id',
        'program_id',
        'graduation_year',
        'graduation_date',
        'academic_year',
        'last_name',
        'first_name',
        'middle_name',
        'gender',
        'degree_name',
        'degree_abbreviation',
        'programme_start_academic_year',
        'programme_end_academic_year',
        'programme_duration_years',
        'classification',
        'gpa',
        'graduation_ceremony_number',
        'certificate_barcode',
        'raw_data',
        'imported_by',
        'roster_import_id',
        'import_diff',
        'import_diff_raw',
        'import_review_status',
        'import_reviewed_by',
        'import_reviewed_at',
        'is_graduated',
    ];

    protected $casts = [
        'is_graduated' => 'boolean',
        'graduation_date' => 'date',
        'gpa' => 'decimal:2',
        'raw_data' => 'array',
        'import_diff' => 'array',
        'import_diff_raw' => 'array',
        'import_reviewed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

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

    public function importReviewedBy()
    {
        return $this->belongsTo(User::class, 'import_reviewed_by');
    }

    public function rosterImport()
    {
        return $this->belongsTo(StudentRosterImport::class, 'roster_import_id');
    }

    public function setLastNameAttribute(?string $value): void
    {
        $this->attributes['last_name'] = $value === null ? null : mb_strtoupper(trim($value));
    }

    public function getFormattedNameAttribute(): string
    {
        if (! $this->last_name) {
            return $this->student?->full_name ?? '';
        }

        $first = $this->first_name ? $this->titleCaseName($this->first_name) : '';
        $middle = $this->middle_name ? $this->titleCaseName($this->middle_name) : '';
        $given = trim($first.' '.$middle);

        return mb_strtoupper($this->last_name).($given ? ', '.$given : '');
    }

    private function titleCaseName(?string $name): string
    {
        if ($name === null) {
            return '';
        }

        return mb_convert_case(trim($name), MB_CASE_TITLE, 'UTF-8');
    }

    public function getSalutationAttribute(): string
    {
        return match ($this->gender) {
            'M' => 'Mr.',
            'F' => 'Ms.',
            default => '',
        };
    }

    public function getEffectiveDegreeNameAttribute(): ?string
    {
        return $this->degree_name ?: $this->program?->degree_name;
    }

    public function getEffectiveDegreeAbbreviationAttribute(): ?string
    {
        return $this->degree_abbreviation ?: $this->program?->degree_abbreviation;
    }

    public function getDegreeWithAbbreviationAttribute(): ?string
    {
        $name = $this->effective_degree_name;

        if (! $name) {
            return null;
        }

        $abbreviation = $this->effective_degree_abbreviation;

        return $abbreviation ? $name.' - ['.$abbreviation.']' : $name;
    }

    public function getOrdinalGraduationCeremonyAttribute(): ?string
    {
        if (! $this->graduation_ceremony_number) {
            return null;
        }

        return DateToWordsHelper::ordinal($this->graduation_ceremony_number);
    }
}
