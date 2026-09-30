<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraduationCeremony extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'sequence_number',
        'ceremony_date',
        'book_cover_path',
    ];

    protected $casts = [
        'ceremony_date' => 'date',
    ];
}
