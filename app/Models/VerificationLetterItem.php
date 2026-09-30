<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationLetterItem extends Model
{
    protected $fillable = [
        'verification_letter_id', 'student_id', 'position', 'name', 'gender', 'number',
        'programme', 'graduation_year', 'barcode', 'study_from', 'study_to', 'duration_years',
    ];

    public function letter()
    {
        return $this->belongsTo(VerificationLetter::class, 'verification_letter_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
