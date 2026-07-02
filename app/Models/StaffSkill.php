<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffSkill extends Model
{
    protected $table = 'staff_skills';
    
    protected $fillable = [
        'staff_id', 'skill_name', 'skill_category', 'proficiency_level',
        'years_of_experience', 'description', 'is_verified'
    ];
    
    protected $casts = [
        'is_verified' => 'boolean',
    ];
    
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}