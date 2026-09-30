<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GateEntryLog extends Model
{
    protected $fillable = [
        'user_id',
        'recorded_by',
        'gate_name',
        'action',
        'verification_method',
        'verification_reference',
        'notes',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
