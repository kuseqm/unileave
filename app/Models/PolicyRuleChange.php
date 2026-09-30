<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyRuleChange extends Model
{
    public const CREATED = 'created';

    public const UPDATED = 'updated';

    public const DELETED = 'deleted';

    /** The rule fields worth keeping in the history. */
    private const TRACKED = ['key', 'value', 'operator', 'condition_field', 'condition_value', 'priority', 'is_active'];

    protected $fillable = [
        'policy_id', 'policy_rule_id', 'key', 'action',
        'old_values', 'new_values', 'reason', 'changed_by',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Changes to the policies a university can see: its own and the platform's.
     */
    public function scopeVisibleTo($query, ?int $universityId)
    {
        return $query->whereHas('policy', fn ($policy) => $policy->visibleTo($universityId));
    }

    /**
     * What to remember about a rule: its meaningful fields, nothing else.
     *
     * @return array<string, mixed>
     */
    public static function snapshot(PolicyRule $rule): array
    {
        return collect(self::TRACKED)
            ->mapWithKeys(fn (string $field) => [$field => $rule->getAttribute($field)])
            ->all();
    }

    /**
     * @param  array<string, mixed>|null  $old
     * @param  array<string, mixed>|null  $new
     */
    public static function record(Policy $policy, PolicyRule $rule, string $action, ?array $old, ?array $new, ?string $reason): self
    {
        return self::create([
            'policy_id' => $policy->id,
            'policy_rule_id' => $rule->id,
            'key' => $rule->key,
            'action' => $action,
            'old_values' => $old,
            'new_values' => $new,
            'reason' => $reason,
            'changed_by' => auth()->id(),
        ]);
    }
}
