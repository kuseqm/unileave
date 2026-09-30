<?php

// app/Models/LeaveBalance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $table = 'leave_balances';

    protected $fillable = [
        'staff_id', 'leave_type_id', 'balance', 'year',
        'carried_forward', 'adjusted_by', 'adjustment_reason',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'carried_forward' => 'decimal:2',
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function adjustedBy()
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    // Scopes
    public function scopeCurrentYear($query)
    {
        return $query->where('year', date('Y'));
    }

    // Helper methods
    public function hasSufficientBalance($days)
    {
        return $this->balance >= $days;
    }

    public function deduct($days)
    {
        $this->decrement('balance', $days);

        return $this;
    }

    public function add($days)
    {
        $this->increment('balance', $days);

        return $this;
    }
}
