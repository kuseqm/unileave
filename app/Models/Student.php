<?php

namespace App\Models;

use App\Helpers\DateToWordsHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'campus_id',
        'registration_number',
        'full_name',
        'last_name',
        'first_name',
        'middle_name',
        'gender',
        'program_id',
        'degree_name',
        'degree_abbreviation',
        'is_graduated',
        'graduation_date',
        'graduation_year',
        'academic_year',
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
    ];

    protected $casts = [
        'is_graduated' => 'boolean',
        'graduation_date' => 'date',
        'gpa' => 'decimal:2',
        'raw_data' => 'array',
        'import_diff' => 'array',
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

    public function rosterImport()
    {
        return $this->belongsTo(StudentRosterImport::class, 'roster_import_id');
    }

    public function importedBy()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function graduationLetters()
    {
        return $this->hasMany(GraduationLetter::class);
    }

    public function graduations()
    {
        return $this->hasMany(StudentGraduation::class);
    }

    public function latestGraduation()
    {
        return $this->hasOne(StudentGraduation::class)->latest('graduation_year');
    }

    public function scopeGraduated($query)
    {
        return $query->where('is_graduated', true);
    }

    /**
     * Surnames are always capitals ("MALECELA") on SJUT documents, however
     * they were typed in a roster or the Edit form.
     */
    public function setLastNameAttribute(?string $value): void
    {
        $this->attributes['last_name'] = $value === null ? null : mb_strtoupper(trim($value));
    }

    /**
     * "MALECELA, Nyagalu Michael" - the exact format SJUT's own real
     * letters use. Falls back to the plain `full_name` for rows imported
     * before name parts existed (or that never had them split out).
     */
    public function getFormattedNameAttribute(): string
    {
        if (! $this->last_name) {
            return $this->full_name ?? '';
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

    /**
     * "Mr." / "Ms." - real letters always prefix one, and default to
     * nothing (not a guess) when gender isn't on file, since a wrong
     * salutation is worse than none.
     */
    public function getSalutationAttribute(): string
    {
        return match ($this->gender) {
            'M' => 'Mr.',
            'F' => 'Ms.',
            default => '',
        };
    }

    public function getPronounAttribute(): string
    {
        return $this->gender === 'F' ? 'she' : 'he';
    }

    public function getPossessivePronounAttribute(): string
    {
        return $this->gender === 'F' ? 'her' : 'his';
    }

    public function getObjectPronounAttribute(): string
    {
        return $this->gender === 'F' ? 'her' : 'him';
    }

    /**
     * What is actually printed as the degree: a value typed for this
     * graduate wins (rare exceptions), otherwise it comes from their
     * Programme - so nobody has to type it 500 times.
     */
    public function getEffectiveDegreeNameAttribute(): ?string
    {
        return $this->degree_name ?: $this->program?->degree_name;
    }

    public function getEffectiveDegreeAbbreviationAttribute(): ?string
    {
        return $this->degree_abbreviation ?: $this->program?->degree_abbreviation;
    }

    /**
     * "Bachelor of Arts with Education - [BAED]" - the abbreviation is
     * optional (not every letter needs it), so this degrades gracefully
     * to just the plain degree name.
     */
    public function getDegreeWithAbbreviationAttribute(): ?string
    {
        $name = $this->effective_degree_name;

        if (! $name) {
            return null;
        }

        $abbreviation = $this->effective_degree_abbreviation;

        return $abbreviation ? $name.' - ['.$abbreviation.']' : $name;
    }

    /**
     * "the Third Graduation Ceremony" - spelled out on demand rather than
     * stored as text, so the wording rule lives in one place
     * (DateToWordsHelper).
     */
    public function getOrdinalGraduationCeremonyAttribute(): ?string
    {
        if (! $this->graduation_ceremony_number) {
            return null;
        }

        return DateToWordsHelper::ordinal($this->graduation_ceremony_number);
    }
}
