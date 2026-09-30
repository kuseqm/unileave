<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'body',
        'category',
        'is_pinned',
        'sort_order',
        'is_active',
        'starts_at',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_active' => 'boolean',
        'starts_at' => 'date',
        'expires_at' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Active, currently within its date window (or with no window set) -
     * what the public landing page should show. Pinned first, then in
     * admin-controlled order, then newest.
     */
    public function scopeVisible($query)
    {
        $today = now()->toDateString();

        return $query->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $today);
            })
            ->orderByDesc('is_pinned')
            ->orderBy('sort_order')
            ->orderByDesc('created_at');
    }

    /**
     * Where this sits right now relative to its active flag and date window:
     * inactive | scheduled (starts_at in the future) | expired | live.
     */
    public function getEffectiveStatusAttribute()
    {
        if (! $this->is_active) {
            return 'inactive';
        }

        $today = now()->toDateString();

        if ($this->starts_at && $this->starts_at->toDateString() > $today) {
            return 'scheduled';
        }

        if ($this->expires_at && $this->expires_at->toDateString() < $today) {
            return 'expired';
        }

        return 'live';
    }
}
