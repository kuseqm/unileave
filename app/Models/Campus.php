<?php

// app/Models/Campus.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;

    protected $table = 'campuses';

    protected $fillable = [
        'university_id', 'name', 'address', 'gps_lat', 'gps_lng', 'code', 'status',
    ];

    protected $casts = [
        'gps_lat' => 'decimal:8',
        'gps_lng' => 'decimal:8',
    ];

    // Relationships
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function staff()
    {
        return $this->hasMany(User::class, 'campus_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
