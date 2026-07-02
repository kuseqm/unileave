<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffContract extends Model
{
    protected $table = 'staff_contracts';
    
    protected $fillable = [
        'staff_id', 'contract_number', 'contract_type', 'start_date', 'end_date',
        'salary', 'salary_currency', 'file_path', 'signed_by', 'is_current', 'remarks'
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'salary' => 'decimal:2',
        'is_current' => 'boolean',
    ];
    
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    
    public function signer()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }
    
    public function getIsExpiredAttribute()
    {
        return $this->end_date && $this->end_date->isPast();
    }
    
    public function getRemainingDaysAttribute()
    {
        if (!$this->end_date) return null;
        return now()->diffInDays($this->end_date, false);
    }
}