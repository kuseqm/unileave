<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffPhone extends Model
{
    protected $table = 'staff_phones';

    protected $fillable = [
        'staff_id', 'phone_number', 'phone_type', 'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
