<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAddress extends Model
{
    protected $table = 'staff_addresses';

    protected $fillable = [
        'staff_id', 'address_type', 'street', 'city', 'state',
        'postal_code', 'country', 'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
