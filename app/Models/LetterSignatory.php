<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Someone who signs outgoing letters (DVCA, DUS...). Their office name heads
 * the letter and their title lines close it, in English or Swahili.
 */
class LetterSignatory extends Model
{
    protected $fillable = [
        'university_id', 'name', 'title_en', 'unit_en', 'title_sw', 'unit_sw',
        'office_en', 'phone', 'email', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function title(string $language): string
    {
        return $language === 'sw' ? $this->title_sw : $this->title_en;
    }

    public function unit(string $language): ?string
    {
        return $language === 'sw' ? $this->unit_sw : $this->unit_en;
    }
}
