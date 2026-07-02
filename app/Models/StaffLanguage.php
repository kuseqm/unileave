<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffLanguage extends Model
{
    protected $table = 'staff_languages';
    
    protected $fillable = [
        'staff_id', 'language_name', 'proficiency_read', 'proficiency_write',
        'proficiency_speak', 'is_working_language'
    ];
    
    protected $casts = [
        'is_working_language' => 'boolean',
    ];
    
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    
    public function getOverallProficiencyAttribute()
    {
        $levels = ['none' => 0, 'basic' => 1, 'intermediate' => 2, 'fluent' => 3, 'native' => 4];
        $avg = ($levels[$this->proficiency_read] + $levels[$this->proficiency_write] + $levels[$this->proficiency_speak]) / 3;
        
        foreach ($levels as $level => $score) {
            if ($avg >= $score) return $level;
        }
        return 'basic';
    }
}