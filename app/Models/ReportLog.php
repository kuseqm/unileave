<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportLog extends Model
{
    protected $table = 'report_logs';

    protected $fillable = [
        'user_id',
        'report_definition_id',
        'format',
        'filters_applied',
        'file_path',
        'qr_code',
        'verification_code',   // ✅ Added for public verification
        'verified_at',         // ✅ Added for tracking first verification
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'filters_applied' => 'array',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reportDefinition()
    {
        return $this->belongsTo(ReportDefinition::class);
    }
}