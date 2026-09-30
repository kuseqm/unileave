<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportSequence extends Model
{
    protected $table = 'import_sequences';

    protected $fillable = [
        'role_code',
        'last_sequence',
    ];

    public $timestamps = true;
}
