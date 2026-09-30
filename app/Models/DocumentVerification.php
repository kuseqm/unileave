<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVerification extends Model
{
    protected $fillable = ['document_id', 'verification_code', 'ip_address', 'user_agent', 'verification_data'];

    protected $casts = ['verification_data' => 'array'];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
