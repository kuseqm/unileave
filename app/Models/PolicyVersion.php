<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyVersion extends Model
{
    use HasFactory;

    protected $table = 'policy_versions';

    protected $fillable = [
        'policy_id', 'version', 'title', 'category', 'scope', 'scope_id',
        'description', 'content', 'document_path', 'status',
        'effective_date', 'review_due_date', 'approved_at', 'meeting_reference',
        'chairperson_name', 'chairperson_signature_path',
        'secretary_name', 'secretary_signature_path',
        'approved_by', 'created_by', 'updated_by', 'sections',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'review_due_date' => 'date',
        'approved_at' => 'datetime',
        'version' => 'integer',
        'sections' => 'array',
    ];

    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function categoryLabel(): string
    {
        return Policy::CATEGORIES[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category));
    }

    public function scopeLabel(): string
    {
        return Policy::SCOPES[$this->scope] ?? ucfirst($this->scope);
    }
}
