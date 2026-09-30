<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalDelegation extends Model
{
    protected $table = 'approval_delegations';

    protected $fillable = [
        'delegator_staff_id',
        'delegate_staff_id',
        'level',
        'start_date',
        'end_date',
        'reason',
        'status',
        'revoked_at',
        'revoked_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'revoked_at' => 'datetime',
    ];

    public function delegator()
    {
        return $this->belongsTo(User::class, 'delegator_staff_id');
    }

    public function delegate()
    {
        return $this->belongsTo(User::class, 'delegate_staff_id');
    }

    public function revokedByUser()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }
}
