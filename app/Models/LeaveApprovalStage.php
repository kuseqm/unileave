<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * The configurable leave-approval chain. Five fixed stage types exist
 * (hod, dean, hr, dvc, hr_logistics) - each keeps its own already-working
 * scope-resolution logic (who exactly "the HOD" is for a given applicant,
 * DVCA vs DVC-PFA, etc). Only the *sequence* and *which stages participate*
 * is configurable here - this is not a generic workflow engine, and adding
 * a genuinely new stage type requires code, not just a row in this table.
 *
 * This is the single source of truth for chain order - LeaveRequestController
 * must resolve "what's next" through here rather than hardcoding transitions,
 * or the config page would silently stop reflecting reality.
 */
class LeaveApprovalStage extends Model
{
    protected $table = 'leave_approval_stages';

    protected $fillable = [
        'stage_key',
        'level',
        'display_name',
        'sequence_order',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sequence_order');
    }

    /**
     * Cached per-request - this is read many times during a single
     * approval action (initial routing, next-level lookup, display names).
     */
    protected static ?Collection $chainCache = null;

    public static function activeChain(): Collection
    {
        return static::$chainCache ??= static::query()->enabled()->ordered()->get();
    }

    /**
     * Call after writing to leave_approval_stages within the same request
     * (e.g. after the admin saves a reorder) so a later read in the same
     * request sees the change instead of the stale cached collection.
     */
    public static function forgetChainCache(): void
    {
        static::$chainCache = null;
    }

    /**
     * The level (LeaveRequest::LEVEL_*) that comes after $level in the
     * currently configured chain, or null if $level is the last enabled
     * stage - or if $level itself isn't in the active chain at all (e.g. it
     * was disabled after a request was already sitting there; callers
     * should treat null defensively, not as "chain complete", in that case).
     */
    public static function nextLevelAfter(?int $level): ?int
    {
        $chain = static::activeChain();
        $index = $chain->search(fn ($stage) => $stage->level === $level);

        if ($index === false || ! isset($chain[$index + 1])) {
            return null;
        }

        return $chain[$index + 1]->level;
    }

    public static function firstLevel(): ?int
    {
        return static::activeChain()->first()?->level;
    }

    public static function lastLevel(): ?int
    {
        return static::activeChain()->last()?->level;
    }

    public static function isLastLevel(?int $level): bool
    {
        return $level !== null && $level === static::lastLevel();
    }

    public static function displayNameFor(?int $level): ?string
    {
        return static::activeChain()->firstWhere('level', $level)?->display_name;
    }

    /**
     * Whether $level is currently part of the active chain at all - false
     * for a level that was just disabled.
     */
    public static function isActive(?int $level): bool
    {
        return $level !== null && static::activeChain()->contains('level', $level);
    }
}
