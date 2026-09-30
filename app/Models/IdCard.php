<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdCard extends Model
{
    protected $fillable = [
        'staff_id',
        'issued_at',
        'valid_until',
        'authorized_by',
        'signature_path',
        'authorized_signature_id',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'valid_until' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function authorizedSignature()
    {
        return $this->belongsTo(AuthorizedSignature::class);
    }
}
