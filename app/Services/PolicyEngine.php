<?php

namespace App\Services;

use App\Models\Policy;
use App\Models\PolicyRule;

/**
 * Evaluates policy rules for a given context, one university at a time.
 *
 * Every lookup resolves in layers, most specific first:
 *
 *   1. the university's own active policies,
 *   2. the platform's active policies (university_id IS NULL) - the default
 *      every university inherits until it publishes something of its own,
 *   3. the hard-coded default the caller passed in.
 *
 * Within a layer the existing rule semantics are unchanged: a rule whose
 * condition matches the context wins (highest priority first), otherwise the
 * layer's unconditional rule applies. A layer with nothing that applies
 * falls through to the next one - so a university can override just one
 * value (say, academic staff's annual leave) and still inherit the rest.
 *
 * The university comes from `$context['university_id']` (callers should pass
 * the *applicant's* - a request submitted on someone's behalf must follow
 * that person's university, not the submitter's), falling back to the signed
 * in user's when it isn't given. Console/queue code with no user simply gets
 * the platform layer.
 */
class PolicyEngine
{
    /**
     * Get a policy value by key.
     *
     * @param  string  $key  The rule key (e.g. 'max_annual_leave').
     * @param  array  $context  Contextual attributes used to match conditions (e.g. ['staff_category' => 'academic']).
     * @param  mixed  $default  Value returned if no matching rule is found. Leave it null to get the
     *                          setting's default from the policy catalog (config/policy_catalog.php) -
     *                          the one place those defaults live.
     * @param  string|null  $category  Optional policy category filter (e.g. 'hr_policy', 'handbook').
     * @return mixed
     */
    public function get(string $key, array $context = [], $default = null, ?string $category = null)
    {
        return $this->resolve($key, $context, $default, $category)['value'];
    }

    /**
     * Like get(), but also says where the value came from - the rule that
     * supplied it and which layer that rule belongs to. This is what the
     * settings screen shows HR; get() is this, minus the explanation.
     *
     * `source` is 'university' (the university's own policy), 'platform' (the
     * platform default it inherits) or 'default' (no rule at all: the caller's
     * default, else the catalog's).
     *
     * @return array{value: mixed, source: string, rule: PolicyRule|null}
     */
    public function resolve(string $key, array $context = [], $default = null, ?string $category = null): array
    {
        foreach ($this->layers($context) as $universityId) {
            $rule = $this->matchingRule($key, $context, $category, $universityId);

            if ($rule) {
                return [
                    'value' => $rule->typedValue(),
                    'source' => $universityId === null ? 'platform' : 'university',
                    'rule' => $rule,
                ];
            }
        }

        return [
            'value' => $default ?? app(PolicyCatalog::class)->default($key),
            'source' => 'default',
            'rule' => null,
        ];
    }

    /**
     * Every active rule that could decide a setting for this university,
     * in the order they are consulted - its own first, then the platform's.
     * Rules are listed whether or not they match the context; the settings
     * screen uses this to show what else is defined behind the value in force.
     *
     * @return array<int, array{source: string, rule: PolicyRule}>
     */
    public function applicableRules(string $key, array $context = [], ?string $category = null): array
    {
        $rules = [];

        foreach ($this->layers($context) as $universityId) {
            foreach ($this->candidateRules($key, $category, $universityId) as $rule) {
                if ($this->policyIsInScope($rule->policy, $context)) {
                    $rules[] = ['source' => $universityId === null ? 'platform' : 'university', 'rule' => $rule];
                }
            }
        }

        return $rules;
    }

    /**
     * Get all active policy values for a category.
     */
    public function getAll(string $category, array $context = []): array
    {
        $values = [];

        // Most specific layer first: `+=` never overwrites a key already
        // set, so the university's value beats the platform's per key.
        foreach ($this->layers($context) as $universityId) {
            $values += $this->layerValues($category, $context, $universityId);
        }

        return $values;
    }

    /**
     * Evaluate a single active policy by its code.
     */
    public function evaluatePolicy(string $code, array $context = []): array
    {
        $policy = $this->findByCode($code, $context);

        if (! $policy || ! $this->policyIsInScope($policy, $context)) {
            return [];
        }

        $values = [];

        foreach ($policy->activeRules as $rule) {
            if ($this->isEmptyCondition($rule) || $rule->matches($context)) {
                $values[$rule->key] = $rule->typedValue();
            }
        }

        return $values;
    }

    /**
     * Find an active policy by code - the university's own if it has one,
     * otherwise the platform's.
     */
    public function policy(string $code, array $context = []): ?Policy
    {
        return $this->findByCode($code, $context);
    }

    /**
     * Shortcut for handbook policies.
     */
    public function handbook(string $code, array $context = []): ?Policy
    {
        return $this->findByCode($code, $context, 'handbook');
    }

    /**
     * Shortcut for examination regulation policies.
     */
    public function examinationRegulation(string $code, array $context = []): ?Policy
    {
        return $this->findByCode($code, $context, 'examination_regulation');
    }

    /**
     * Check whether a policy is currently active and effective.
     */
    public function isPolicyEffective(Policy $policy): bool
    {
        if (! $policy->isActive()) {
            return false;
        }

        if ($policy->effective_date && $policy->effective_date->isFuture()) {
            return false;
        }

        return true;
    }

    /**
     * The universities to consult, in order: the context's (or the signed
     * in user's) own, then null for the platform layer.
     *
     * @return array<int, int|null>
     */
    protected function layers(array $context): array
    {
        $universityId = array_key_exists('university_id', $context)
            ? $context['university_id']
            : auth()->user()?->university_id;

        return $universityId ? [(int) $universityId, null] : [null];
    }

    /**
     * The rule that applies within a single layer, or null: a conditional
     * rule that matches (highest priority first), else that layer's
     * unconditional default.
     */
    protected function matchingRule(string $key, array $context, ?string $category, ?int $universityId): ?PolicyRule
    {
        $defaultRule = null;

        foreach ($this->candidateRules($key, $category, $universityId) as $rule) {
            if (! $this->policyIsInScope($rule->policy, $context)) {
                continue;
            }

            if ($this->isEmptyCondition($rule)) {
                $defaultRule ??= $rule;

                continue;
            }

            if ($rule->matches($context)) {
                return $rule;
            }
        }

        return $defaultRule;
    }

    /**
     * Every value a single layer defines for a category, conditional
     * matches taking precedence over that layer's unconditional defaults.
     */
    protected function layerValues(string $category, array $context, ?int $universityId): array
    {
        $rules = PolicyRule::with('policy')
            ->where('is_active', true)
            ->whereHas('policy', fn ($query) => $this->constrainPolicies($query, $universityId, $category))
            ->orderByDesc('priority')
            ->get();

        $values = [];
        $defaults = [];

        foreach ($rules as $rule) {
            if (! $this->policyIsInScope($rule->policy, $context)) {
                continue;
            }

            if ($this->isEmptyCondition($rule)) {
                $defaults[$rule->key] ??= $rule->typedValue();

                continue;
            }

            if ($rule->matches($context)) {
                $values[$rule->key] ??= $rule->typedValue();
            }
        }

        return $values + $defaults;
    }

    protected function findByCode(string $code, array $context, ?string $category = null): ?Policy
    {
        foreach ($this->layers($context) as $universityId) {
            $query = Policy::where('code', $code);
            $this->constrainPolicies($query, $universityId, $category);

            if ($policy = $query->first()) {
                return $policy;
            }
        }

        return null;
    }

    /**
     * Restrict a Policy query to what can actually apply right now: active,
     * already in effect (a published policy with a future effective date
     * starts applying on that date, not the moment it's published), and
     * belonging to exactly this layer.
     */
    protected function constrainPolicies($query, ?int $universityId, ?string $category = null)
    {
        $query->active()->effective();

        $universityId === null
            ? $query->whereNull('university_id')
            : $query->where('university_id', $universityId);

        if ($category) {
            $query->where('category', $category);
        }

        return $query;
    }

    /**
     * Determine whether a policy's scope matches the given context.
     */
    protected function policyIsInScope(Policy $policy, array $context): bool
    {
        if ($policy->scope === 'university-wide' || empty($policy->scope)) {
            return true;
        }

        $scopeId = $policy->scope_id;

        switch ($policy->scope) {
            case 'school':
                return $scopeId !== null && (int) ($context['school_id'] ?? null) === (int) $scopeId;
            case 'department':
                return $scopeId !== null && (int) ($context['department_id'] ?? null) === (int) $scopeId;
            case 'role':
                return $scopeId !== null && ! empty($context['role']) && (string) $context['role'] === (string) $scopeId;
            default:
                return true;
        }
    }

    protected function candidateRules(string $key, ?string $category, ?int $universityId)
    {
        return PolicyRule::with('policy')
            ->where('key', $key)
            ->where('is_active', true)
            ->whereHas('policy', fn ($query) => $this->constrainPolicies($query, $universityId, $category))
            ->orderByDesc('priority')
            ->get();
    }

    protected function isEmptyCondition(PolicyRule $rule): bool
    {
        return empty($rule->condition_field);
    }
}
