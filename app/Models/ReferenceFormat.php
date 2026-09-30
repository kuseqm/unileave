<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReferenceFormat extends Model
{
    use HasFactory;

    protected $table = 'reference_formats';

    protected $fillable = [
        'name',
        'university_id',
        'entity_type',
        'entity_id',
        'document_type',
        'action_type',
        'format_pattern',
        'separator',
        'serial_length',
        'serial_start',
        'current_serial',
        'file_capacity',
        'volume_start',
        'file_naming_pattern',
        'auto_create_new_file',
        'track_physical_location',
        'is_active',
    ];

    protected $casts = [
        'serial_start' => 'integer',
        'current_serial' => 'integer',
        'serial_length' => 'integer',
        'file_capacity' => 'integer',
        'auto_create_new_file' => 'boolean',
        'track_physical_location' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Get the university that owns this format
     */
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    /**
     * Get the associated department (polymorphic)
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'entity_id')->where('entity_type', 'department');
    }

    /**
     * Get the associated school (polymorphic)
     */
    public function school()
    {
        return $this->belongsTo(School::class, 'entity_id')->where('entity_type', 'school');
    }

    /**
     * Get the associated entity (polymorphic)
     */
    public function entity()
    {
        if ($this->entity_type === 'department') {
            return $this->belongsTo(Department::class, 'entity_id');
        }
        if ($this->entity_type === 'school') {
            return $this->belongsTo(School::class, 'entity_id');
        }

        return null;
    }

    /**
     * Get letter files for this format
     */
    public function letterFiles()
    {
        return $this->hasMany(LetterFile::class, 'reference_format_id');
    }

    /**
     * Get the active letter file
     */
    public function activeFile()
    {
        return $this->hasOne(LetterFile::class, 'reference_format_id')->where('is_active', true);
    }

    // ============================================
    // ACCESSORS
    // ============================================

    /**
     * Get document type label
     */
    public function getDocumentTypeLabelAttribute()
    {
        $labels = [
            'leave' => 'Leave Request',
            'permission' => 'Permission Request',
            'travel' => 'Travel Permission',
            'contract' => 'Staff Contract',
            'general' => 'General Correspondence',
            'graduation' => 'Graduation Confirmation Letter',
            'verification' => 'Certificate / Transcript Verification Letter',
        ];

        return $labels[$this->document_type] ?? ucfirst($this->document_type);
    }

    /**
     * Get document type abbreviation
     */
    public function getDocumentTypeAbbreviationAttribute()
    {
        $abbr = [
            'leave' => 'LVE',
            'permission' => 'PER',
            'travel' => 'TRV',
            'contract' => 'CTR',
            'general' => 'GEN',
            'graduation' => 'GRD',
            'verification' => 'VER',
        ];

        return $abbr[$this->document_type] ?? 'DOC';
    }

    /**
     * Get document type numeric code
     */
    public function getDocumentTypeCodeAttribute()
    {
        $codes = [
            'leave' => '013',
            'permission' => '014',
            'travel' => '015',
            'contract' => '016',
            'general' => '017',
            'graduation' => '018',
            'verification' => '013',
        ];

        return $codes[$this->document_type] ?? '000';
    }

    /**
     * Get entity type label
     */
    public function getEntityTypeLabelAttribute()
    {
        $labels = [
            'department' => 'Department',
            'school' => 'School/Faculty',
        ];

        return $labels[$this->entity_type] ?? 'None';
    }

    /**
     * Get entity code
     */
    public function getEntityCodeAttribute()
    {
        if ($this->entity_type === 'department' && $this->entity_id) {
            $department = Department::find($this->entity_id);

            return $department->code ?? 'DEPT';
        }

        if ($this->entity_type === 'school' && $this->entity_id) {
            $school = School::find($this->entity_id);

            return $school->code ?? 'SCH';
        }

        return 'GEN';
    }

    /**
     * Get entity name
     */
    public function getEntityNameAttribute()
    {
        if ($this->entity_type === 'department' && $this->entity_id) {
            $department = Department::find($this->entity_id);

            return $department->name ?? 'N/A';
        }

        if ($this->entity_type === 'school' && $this->entity_id) {
            $school = School::find($this->entity_id);

            return $school->name ?? 'N/A';
        }

        return 'General';
    }

    /**
     * Get next serial number as formatted string
     */
    public function getNextSerialFormattedAttribute()
    {
        $nextSerial = $this->current_serial + 1;

        return str_pad($nextSerial, $this->serial_length, '0', STR_PAD_LEFT);
    }

    /**
     * Get current serial as formatted string
     */
    public function getCurrentSerialFormattedAttribute()
    {
        return str_pad($this->current_serial, $this->serial_length, '0', STR_PAD_LEFT);
    }

    /**
     * Check if format has capacity for more letters
     */
    public function getHasCapacityAttribute()
    {
        if ($this->file_capacity == 0) {
            return true;
        }

        return $this->current_serial < $this->file_capacity;
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope for active formats only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for a specific document type
     */
    public function scopeForDocument($query, $documentType)
    {
        return $query->where('document_type', $documentType);
    }

    /**
     * Scope for a specific university
     */
    public function scopeForUniversity($query, $universityId)
    {
        return $query->where('university_id', $universityId);
    }

    /**
     * Scope for a specific entity
     */
    public function scopeForEntity($query, $entityType, $entityId)
    {
        return $query->where('entity_type', $entityType)->where('entity_id', $entityId);
    }

    // ============================================
    // NUMBER GENERATION METHODS
    // ============================================

    /**
     * Generate a reference number based on the format pattern
     */
    public function generateNumber($volumeNumber = null)
    {
        $year = date('Y');
        $nextSerial = $this->current_serial + 1;
        $serial = str_pad($nextSerial, $this->serial_length, '0', STR_PAD_LEFT);

        // Get university code
        $universityCode = $this->university->code ?? 'UNIV';

        // Get entity code
        $entityCode = $this->entity_code;

        // Get document type values
        $typeAbbreviation = $this->document_type_abbreviation;
        $typeCode = $this->document_type_code;

        // Calculate volume based on capacity
        if ($volumeNumber) {
            $volume = 'VOL '.$volumeNumber;
        } else {
            $currentVolume = floor($this->current_serial / max($this->file_capacity, 1));
            $volume = 'VOL '.$this->numberToRoman($currentVolume + 1);
        }

        // Get action type
        $actionType = $this->action_type ?? 'APR';

        // Replace all placeholders
        $reference = str_replace(
            [
                '{year}',
                '{serial}',
                '{prefix}',
                '{type}',
                '{entity_code}',
                '{university_code}',
                '{volume}',
                '{type_code}',
                '{action_type}',
            ],
            [
                $year,
                $serial,
                $universityCode,
                $typeAbbreviation,
                $entityCode,
                $universityCode,
                $volume,
                $typeCode,
                $actionType,
            ],
            $this->format_pattern
        );

        return $reference;
    }

    /**
     * Generate and increment the serial number
     */
    public function generateAndIncrement($volumeNumber = null)
    {
        $reference = $this->generateNumber($volumeNumber);

        // Increment the serial
        $this->increment('current_serial');

        return $reference;
    }

    /**
     * The next reference number for a volume-based file (e.g. "SJUT/SVAC/013/04/VOL IV/181"):
     * the serial counts within the current volume and starts again at 1 when the
     * volume reaches its capacity, and the volume number moves on with it. Uses a
     * row lock so two people generating letters at once never get the same number.
     */
    public function generateForCurrentVolume(): string
    {
        return DB::transaction(function () {
            $format = static::whereKey($this->id)->lockForUpdate()->firstOrFail();

            $reference = $format->referenceForIssuedCount((int) $format->current_serial);

            $format->increment('current_serial');

            return $reference;
        });
    }

    /** The reference the next letter will get - shown to the person before they generate it. */
    public function peekNextForCurrentVolume(): string
    {
        return $this->referenceForIssuedCount((int) $this->current_serial);
    }

    private function referenceForIssuedCount(int $issued): string
    {
        $capacity = max((int) $this->file_capacity, 0);
        $volume = max((int) $this->volume_start, 1) + ($capacity > 0 ? intdiv($issued, $capacity) : 0);
        $serialInVolume = $capacity > 0 ? ($issued % $capacity) + 1 : $issued + 1;

        return str_replace(
            ['{year}', '{serial}', '{prefix}', '{type}', '{entity_code}', '{university_code}', '{volume}', '{type_code}', '{action_type}'],
            [
                date('Y'),
                str_pad($serialInVolume, $this->serial_length, '0', STR_PAD_LEFT),
                $this->university->code ?? 'UNIV',
                $this->document_type_abbreviation,
                $this->entity_code,
                $this->university->code ?? 'UNIV',
                'VOL '.$this->numberToRoman($volume),
                $this->document_type_code,
                $this->action_type ?? 'APR',
            ],
            $this->format_pattern
        );
    }

    /**
     * Generate a preview of the format (without incrementing)
     */
    public function previewNumber($serialNumber = null)
    {
        $year = date('Y');
        $serial = $serialNumber ? str_pad($serialNumber, $this->serial_length, '0', STR_PAD_LEFT) : '0001';

        $universityCode = $this->university->code ?? 'UNIV';
        $entityCode = $this->entity_code;
        $typeAbbreviation = $this->document_type_abbreviation;
        $typeCode = $this->document_type_code;
        $volume = 'VOL I';
        $actionType = $this->action_type ?? 'APR';

        return str_replace(
            [
                '{year}', '{serial}', '{prefix}', '{type}', '{entity_code}',
                '{university_code}', '{volume}', '{type_code}', '{action_type}',
            ],
            [
                $year, $serial, $universityCode, $typeAbbreviation, $entityCode,
                $universityCode, $volume, $typeCode, $actionType,
            ],
            $this->format_pattern
        );
    }

    /**
     * Get the current volume number based on serial
     */
    public function getCurrentVolume()
    {
        if ($this->file_capacity == 0) {
            return 1;
        }

        return floor($this->current_serial / $this->file_capacity) + 1;
    }

    /**
     * Get the current volume as Roman numeral
     */
    public function getCurrentVolumeRoman()
    {
        return $this->numberToRoman($this->getCurrentVolume());
    }

    /**
     * Convert number to Roman numerals
     */
    public function numberToRoman($number)
    {
        $map = [
            1000 => 'M', 900 => 'CM', 500 => 'D', 400 => 'CD',
            100 => 'C', 90 => 'XC', 50 => 'L', 40 => 'XL',
            10 => 'X', 9 => 'IX', 5 => 'V', 4 => 'IV', 1 => 'I',
        ];

        $result = '';
        foreach ($map as $value => $numeral) {
            while ($number >= $value) {
                $result .= $numeral;
                $number -= $value;
            }
        }

        return $result;
    }
}
