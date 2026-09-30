<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key', 'value', 'type', 'group', 'label', 'description',
        'options', 'role', 'user_id', 'tenant_id', 'sort_order', 'is_encrypted',
    ];

    protected $casts = [
        'options' => 'array',
        'is_encrypted' => 'boolean',
    ];

    /**
     * Get setting value with proper casting
     */
    public function getTypedValueAttribute()
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return floatval($this->value);
            case 'json':
                return json_decode($this->value, true);
            default:
                if ($this->is_encrypted && $this->value) {
                    try {
                        return decrypt($this->value);
                    } catch (\Exception $e) {
                        return $this->value;
                    }
                }

                return $this->value;
        }
    }

    /**
     * Scope for global settings
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('role')->whereNull('user_id')->whereNull('tenant_id');
    }

    /**
     * Scope for role settings
     */
    public function scopeForRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for user settings
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
