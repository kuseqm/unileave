<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffEmail extends Model
{
    protected $table = 'staff_emails';
    
    protected $fillable = [
        'staff_id', 'email', 'email_type', 'is_primary'
    ];
    
    protected $casts = [
        'is_primary' => 'boolean',
    ];
    
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}