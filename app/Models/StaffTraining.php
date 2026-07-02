<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffTraining extends Model
{
    protected $table = 'staff_trainings';
    
    protected $fillable = [
        'staff_id', 'training_title', 'training_provider', 'training_type',
        'start_date', 'end_date', 'duration_days', 'certificate_file'
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}