<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportDefinition extends Model
{
    protected $fillable = [
        'name', 'display_name', 'description', 'filters', 'columns',
        'chart_config', 'permission', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'filters' => 'array',
        'columns' => 'array',
        'chart_config' => 'array',
        'is_active' => 'boolean',
    ];

    public function logs()
    {
        return $this->hasMany(ReportLog::class);
    }
}
