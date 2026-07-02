<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    
    protected $fillable = [
        'user_id', 'action', 'module', 'record_id', 
        'old_values', 'new_values', 'ip_address', 'user_agent'
    ];
    
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public static function log($action, $module, $recordId = null, $oldValues = null, $newValues = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}