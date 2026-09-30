<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorizedSignature extends Model
{
    protected $fillable = [
        'name',
        'signature_path',
    ];

    public function idCards()
    {
        return $this->hasMany(IdCard::class);
    }
}
