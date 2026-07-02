<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffReferee extends Model
{
    protected $table = 'staff_referees';
    
    protected $fillable = [
        'staff_id', 'referee_name', 'referee_title', 'referee_organization',
        'referee_email', 'referee_phone', 'relationship', 'years_known', 'is_contact_allowed'
    ];
    
    protected $casts = [
        'is_contact_allowed' => 'boolean',
    ];
    
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}