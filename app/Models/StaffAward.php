<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAward extends Model
{
    protected $table = 'staff_awards';

    protected $fillable = [
        'staff_id', 'award_name', 'awarding_body', 'award_date',
        'award_level', 'description', 'certificate_file',
    ];

    protected $casts = [
        'award_date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
