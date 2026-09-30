<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterFile extends Model
{
    use HasFactory;

    protected $table = 'letter_files';

    protected $fillable = [
        'reference_format_id',
        'file_number',
        'file_name',
        'start_serial',
        'end_serial',
        'current_serial',
        'is_active',
        'physical_location',
        'opened_at',
        'closed_at',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'start_serial' => 'integer',
        'end_serial' => 'integer',
        'current_serial' => 'integer',
        'is_active' => 'boolean',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Get the reference format this file belongs to
     */
    public function referenceFormat()
    {
        return $this->belongsTo(ReferenceFormat::class, 'reference_format_id');
    }

    /**
     * Get the user who created this file
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get letters in this file
     */
    public function letters()
    {
        return $this->hasMany(Letter::class, 'file_number', 'file_number');
    }

    // ============================================
    // ACCESSORS
    // ============================================

    /**
     * Get file number as formatted string
     */
    public function getFormattedFileNumberAttribute()
    {
        return $this->file_number;
    }

    /**
     * Get the current serial as formatted string
     */
    public function getCurrentSerialFormattedAttribute()
    {
        if ($this->referenceFormat) {
            return str_pad($this->current_serial, $this->referenceFormat->serial_length, '0', STR_PAD_LEFT);
        }

        return str_pad($this->current_serial, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get start serial as formatted string
     */
    public function getStartSerialFormattedAttribute()
    {
        if ($this->referenceFormat) {
            return str_pad($this->start_serial, $this->referenceFormat->serial_length, '0', STR_PAD_LEFT);
        }

        return str_pad($this->start_serial, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get end serial as formatted string
     */
    public function getEndSerialFormattedAttribute()
    {
        if ($this->end_serial) {
            if ($this->referenceFormat) {
                return str_pad($this->end_serial, $this->referenceFormat->serial_length, '0', STR_PAD_LEFT);
            }

            return str_pad($this->end_serial, 4, '0', STR_PAD_LEFT);
        }

        return 'Unlimited';
    }

    /**
     * Get usage percentage
     */
    public function getUsagePercentageAttribute()
    {
        if (! $this->end_serial || $this->end_serial == 0) {
            return 0;
        }
        $total = $this->end_serial - $this->start_serial + 1;
        $used = $this->current_serial - $this->start_serial + 1;

        return round(($used / $total) * 100);
    }

    /**
     * Get remaining capacity
     */
    public function getRemainingCapacityAttribute()
    {
        if (! $this->end_serial) {
            return 'Unlimited';
        }
        $remaining = $this->end_serial - $this->current_serial;

        return max(0, $remaining);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        if ($this->is_active && ! $this->closed_at) {
            if ($this->end_serial && $this->current_serial >= $this->end_serial) {
                return 'Full';
            }

            return 'Active';
        }

        return 'Closed';
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'Active' => 'success',
            'Full' => 'warning',
            'Closed' => 'secondary',
        ];

        return $colors[$this->status_label] ?? 'secondary';
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope for active files only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNull('closed_at');
    }

    /**
     * Scope for closed files
     */
    public function scopeClosed($query)
    {
        return $query->where('is_active', false)->orWhereNotNull('closed_at');
    }

    /**
     * Scope for files with capacity remaining
     */
    public function scopeHasCapacity($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('end_serial')
                ->orWhereColumn('current_serial', '<', 'end_serial');
        });
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Check if file has capacity for more letters
     */
    public function hasCapacity()
    {
        if (! $this->end_serial) {
            return true;
        }

        return $this->current_serial < $this->end_serial;
    }

    /**
     * Check if file is full
     */
    public function isFull()
    {
        return $this->end_serial && $this->current_serial >= $this->end_serial;
    }

    /**
     * Generate the next letter number in this file
     */
    public function getNextLetterNumber()
    {
        if (! $this->hasCapacity()) {
            return null;
        }

        $nextSerial = $this->current_serial + 1;

        if ($this->referenceFormat) {
            return $this->referenceFormat->generateNumber($nextSerial);
        }

        return str_pad($nextSerial, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Increment the current serial
     */
    public function incrementSerial()
    {
        $this->increment('current_serial');

        // Check if file is now full
        if ($this->isFull()) {
            $this->update(['is_active' => false, 'closed_at' => now()]);

            // Auto-create new file if format allows
            if ($this->referenceFormat && $this->referenceFormat->auto_create_new_file) {
                $this->referenceFormat->createNewFile();
            }
        }

        return $this;
    }

    /**
     * Close the file
     */
    public function close()
    {
        $this->update([
            'is_active' => false,
            'closed_at' => now(),
        ]);

        return $this;
    }

    /**
     * Reopen a closed file
     */
    public function reopen()
    {
        $this->update([
            'is_active' => true,
            'closed_at' => null,
        ]);

        return $this;
    }

    /**
     * Get the range display
     */
    public function getRangeDisplay()
    {
        $start = $this->start_serial_formatted;
        $end = $this->end_serial_formatted;

        if ($end === 'Unlimited') {
            return "From {$start} onward";
        }

        return "{$start} - {$end}";
    }

    /**
     * Get the file number with volume indicator
     */
    public function getFileNumberDisplay()
    {
        $parts = explode('-', $this->file_number);
        $volume = end($parts);

        return "File: {$this->file_number}";
    }
}
