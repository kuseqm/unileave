<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeActionOfficer extends Model
{
    use HasFactory;

    protected $table = 'office_action_officers';

    protected $fillable = [
        'office_id',
        'staff_id',
        'role_in_office',
        'is_default',
        'can_approve',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'can_approve' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Office this assignment belongs to
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    /**
     * Staff member assigned as action officer
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    // ========== SCOPES ==========

    /**
     * Scope: Only default action officers
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope: Only those who can approve
     */
    public function scopeCanApprove($query)
    {
        return $query->where('can_approve', true);
    }
}