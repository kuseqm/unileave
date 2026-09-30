<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequestDraft extends Model
{
    protected $fillable = [
        'staff_id',
        'current_step',
        'form_data',
        'signature_data',
    ];

    protected $casts = [
        'form_data' => 'array',
        'current_step' => 'integer',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
