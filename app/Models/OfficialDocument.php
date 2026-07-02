<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficialDocument extends Model
{
    protected $table = 'official_documents';

    protected $fillable = [
        'document_number', 'type', 'related_id', 'related_type',
        'generated_by', 'file_path', 'qr_code_path', 'qr_code_data',
        'digital_signature_path', 'is_signed', 'signed_at', 'signed_by',
        'verification_url', 'status'
    ];

    protected $casts = [
        'is_signed' => 'boolean',
        'signed_at' => 'datetime',
    ];

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function related()
    {
        return $this->morphTo();
    }

    public function verifications()
    {
        return $this->hasMany(DocumentVerification::class, 'official_document_id');
    }
}