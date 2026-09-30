<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AcademicYear extends Model
{
    protected $fillable = [
        'label',
        'start_date',
        'end_date',
        'semester_1_name',
        'semester_1_start',
        'semester_1_end',
        'semester_2_name',
        'semester_2_start',
        'semester_2_end',
        'current_semester',
        'is_current',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'semester_1_start' => 'date',
        'semester_1_end' => 'date',
        'semester_2_start' => 'date',
        'semester_2_end' => 'date',
        'is_current' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * The one active academic year, or null if none has been activated yet.
     * Cached per-request since this is read on every page (breadcrumb badge).
     */
    public static function current(): ?self
    {
        static $cached = false;

        if ($cached === false) {
            $cached = static::current_query()->first();
        }

        return $cached;
    }

    protected static function current_query()
    {
        return static::where('is_current', true);
    }

    /**
     * Makes this the one active academic year - deactivates every other
     * row first, in a transaction, so there is never a moment (or a bug)
     * with two "current" years at once.
     */
    public function activate(): void
    {
        DB::transaction(function () {
            static::where('id', '!=', $this->id)->update(['is_current' => false]);
            $this->update(['is_current' => true]);
        });
    }

    public function getCurrentSemesterNameAttribute(): string
    {
        return $this->current_semester === 2 ? $this->semester_2_name : $this->semester_1_name;
    }

    public function getCurrentSemesterRangeAttribute(): array
    {
        return $this->current_semester === 2
            ? [$this->semester_2_start, $this->semester_2_end]
            : [$this->semester_1_start, $this->semester_1_end];
    }
}
