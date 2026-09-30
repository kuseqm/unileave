<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use App\Models\Policy;
use App\Models\PolicyRule;
use App\Models\PolicyRuleChange;
use App\Services\PolicyCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PolicyRuleController extends Controller
{
    public function __construct(private PolicyCatalog $catalog) {}

    public function index(Policy $policy)
    {
        $rules = $policy->rules()->with('creator')->orderByDesc('priority')->paginate(10);
        $catalog = $this->catalog;

        return view('policies.rules.index', compact('policy', 'rules', 'catalog'));
    }

    public function create(Policy $policy)
    {
        $editorCatalog = $this->catalog->forEditor($policy->category);

        if (empty($editorCatalog)) {
            return redirect()->route('policies.show', $policy)
                ->with('error', "There are no settings to configure for {$policy->categoryLabel()} policies.");
        }

        return view('policies.rules.create', [
            'policy' => $policy,
            'catalog' => $this->catalog,
            'editorCatalog' => $editorCatalog,
        ]);
    }

    public function store(Request $request, Policy $policy)
    {
        ['rule' => $validated, 'reason' => $reason] = $this->validatedData($request, $policy);

        DB::transaction(function () use ($validated, $policy, $reason) {
            $rule = new PolicyRule($validated);
            $rule->policy_id = $policy->id;
            $rule->created_by = auth()->id();
            $rule->save();

            $this->recordChange($policy, $rule, PolicyRuleChange::CREATED, null, PolicyRuleChange::snapshot($rule), $reason);
        });

        return redirect()->route('policies.show', $policy)
            ->with('success', 'Policy rule added successfully.');
    }

    public function edit(Policy $policy, PolicyRule $rule)
    {
        return view('policies.rules.edit', [
            'policy' => $policy,
            'rule' => $rule,
            'catalog' => $this->catalog,
            'editorCatalog' => $this->catalog->forEditor($policy->category),
        ]);
    }

    public function update(Request $request, Policy $policy, PolicyRule $rule)
    {
        ['rule' => $validated, 'reason' => $reason] = $this->validatedData($request, $policy, $rule);

        DB::transaction(function () use ($validated, $policy, $rule, $reason) {
            $before = PolicyRuleChange::snapshot($rule);

            $rule->fill($validated);
            $rule->updated_by = auth()->id();
            $rule->save();

            // Saving without changing anything isn't worth a history entry.
            if ($before !== PolicyRuleChange::snapshot($rule)) {
                $this->recordChange($policy, $rule, PolicyRuleChange::UPDATED, $before, PolicyRuleChange::snapshot($rule), $reason);
            }
        });

        return redirect()->route('policies.show', $policy)
            ->with('success', 'Policy rule updated successfully.');
    }

    public function destroy(Request $request, Policy $policy, PolicyRule $rule)
    {
        $reason = $request->validate(['reason' => $this->reasonRules($policy)])['reason'] ?? null;

        DB::transaction(function () use ($policy, $rule, $reason) {
            $this->recordChange($policy, $rule, PolicyRuleChange::DELETED, PolicyRuleChange::snapshot($rule), null, $reason);

            $rule->delete();
        });

        return redirect()->route('policies.show', $policy)
            ->with('success', 'Policy rule deleted successfully.');
    }

    /**
     * Every change ever made to this policy's rules, newest first: what a
     * setting was, what it became, who changed it and why.
     */
    public function history(Policy $policy)
    {
        $changes = $policy->ruleChanges()->with('changer')->latest('id')->paginate(15);
        $catalog = $this->catalog;

        return view('policies.rules.history', compact('policy', 'changes', 'catalog'));
    }

    /**
     * Validate a rule against the policy catalog: the setting must be one the
     * system knows for this policy's category, the value must suit that
     * setting (type, range, allowed choices), and the condition may only use
     * fields and values the setting supports. What's stored (type, label) is
     * taken from the catalog, never from the form, so a rule can't claim to be
     * something its setting isn't.
     */
    private function validatedData(Request $request, Policy $policy, ?PolicyRule $existing = null): array
    {
        // A rule's setting is fixed once created - changing it would turn
        // "45 days of leave" into some other setting's value.
        $input = $existing ? array_merge($request->all(), ['key' => $existing->key]) : $request->all();

        $key = (string) ($input['key'] ?? '');
        $definition = $this->catalog->definition($key);
        $allowedKeys = array_keys($this->catalog->keys($policy->category));

        $rules = [
            'key' => ['required', Rule::in($allowedKeys)],
            'value' => $definition ? $this->valueRules($definition) : ['required'],
            'condition_field' => ['nullable', Rule::in(array_keys($this->catalog->conditionFields($key)))],
            'operator' => ['nullable', Rule::in(array_keys(PolicyCatalog::OPERATORS))],
            'condition_value' => ['nullable', 'string', 'max:500'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'reason' => $this->reasonRules($policy),
        ];

        $messages = [
            'key.in' => 'Choose one of the settings listed - the system only reads those.',
            'condition_field.in' => 'This setting cannot be limited by that field.',
        ];

        $conditionValue = null;

        $validated = validator($input, $rules, $messages)
            ->after(function (Validator $validator) use ($input, $key, $policy, $existing, &$conditionValue) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $conditionValue = $this->checkCondition($validator, $input, $key);

                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $this->checkNotDuplicate($validator, $input, $conditionValue, $policy, $existing);
            })
            ->validate();

        $hasCondition = ! empty($validated['condition_field']);

        return [
            'rule' => [
                'key' => $key,
                'label' => $definition['label'],
                'value' => $this->normalisedValue($definition, $validated['value']),
                'type' => $this->catalog->storedType($key),
                'condition_field' => $hasCondition ? $validated['condition_field'] : null,
                'operator' => $hasCondition ? ($validated['operator'] ?? 'eq') : null,
                'condition_value' => $hasCondition ? $conditionValue : null,
                // A rule with a condition should outrank the everyone-rule it refines.
                'priority' => $validated['priority'] ?? ($hasCondition ? 10 : 0),
                'is_active' => $request->boolean('is_active', false),
            ],
            'reason' => filled($validated['reason'] ?? null) ? trim($validated['reason']) : null,
        ];
    }

    /**
     * A change to a policy already in force has to say why - it takes effect
     * immediately and without re-approval, so the reason is the accountability.
     * While a policy is still a draft nothing is in force yet, so it's optional.
     *
     * @return array<int, string>
     */
    private function reasonRules(Policy $policy): array
    {
        return [$policy->isActive() ? 'required' : 'nullable', 'string', 'min:5', 'max:1000'];
    }

    /**
     * Remember the change in the policy's own history, and in the
     * organisation-wide audit trail.
     *
     * @param  array<string, mixed>|null  $old
     * @param  array<string, mixed>|null  $new
     */
    private function recordChange(Policy $policy, PolicyRule $rule, string $action, ?array $old, ?array $new, ?string $reason): void
    {
        PolicyRuleChange::record($policy, $rule, $action, $old, $new, $reason);

        AuditLogHelper::log($action, 'policy_rule', $rule->id, $old, ($new ?? []) + ['policy' => $policy->code, 'reason' => $reason]);
    }

    /**
     * @param  array<string, mixed>  $definition
     * @return array<int, mixed>
     */
    private function valueRules(array $definition): array
    {
        return match ($definition['type']) {
            'numeric' => array_filter([
                'required',
                'numeric',
                ($definition['integer'] ?? false) ? 'integer' : null,
                isset($definition['min']) ? 'min:'.$definition['min'] : null,
                isset($definition['max']) ? 'max:'.$definition['max'] : null,
            ]),
            'boolean' => ['required', Rule::in(['true', 'false', '1', '0'])],
            'select' => ['required', Rule::in(array_keys($definition['options']))],
            'time' => ['required', 'date_format:H:i'],
            default => ['required', 'string', 'max:255'],
        };
    }

    private function normalisedValue(array $definition, mixed $value): string
    {
        return match ($definition['type']) {
            'boolean' => in_array((string) $value, ['true', '1'], true) ? 'true' : 'false',
            'numeric' => (string) (($definition['integer'] ?? false) ? (int) $value : $value + 0),
            default => (string) $value,
        };
    }

    /**
     * A condition needs a field, at least one value, and each value must be
     * one the field really has. "is"/"is not" take a single value; "is any of"
     * takes several.
     *
     * @return string|null the cleaned condition value (comma-separated for "is any of")
     */
    private function checkCondition(Validator $validator, array $input, string $key): ?string
    {
        $field = $input['condition_field'] ?? null;

        if (empty($field)) {
            return null;
        }

        $operator = ($input['operator'] ?? null) ?: 'eq';
        $values = array_values(array_unique(array_filter(array_map('trim', explode(',', (string) ($input['condition_value'] ?? ''))), 'strlen')));

        if ($values === []) {
            $validator->errors()->add('condition_value', 'Choose the value this rule applies to.');

            return null;
        }

        if ($operator !== 'in' && count($values) > 1) {
            $validator->errors()->add('condition_value', 'Choose a single value, or switch to "is any of".');

            return null;
        }

        $options = $this->catalog->conditionOptions($field);

        foreach ($values as $value) {
            if ($options !== [] && ! array_key_exists($value, $options)) {
                $validator->errors()->add('condition_value', "\"{$value}\" is not a valid choice for this condition.");

                return null;
            }
        }

        return implode(',', $values);
    }

    /**
     * The same setting may appear several times in one policy with different
     * conditions ("45 for academic staff", "28 for everyone else"), but not
     * twice with the same condition - that would leave two rules disagreeing.
     */
    private function checkNotDuplicate(Validator $validator, array $input, ?string $conditionValue, Policy $policy, ?PolicyRule $existing): void
    {
        $field = ($input['condition_field'] ?? null) ?: null;
        $operator = $field ? (($input['operator'] ?? null) ?: 'eq') : null;

        $sameCondition = $policy->rules()
            ->where('key', $input['key'])
            ->when($existing, fn ($query) => $query->whereKeyNot($existing->id))
            ->get()
            ->contains(function (PolicyRule $other) use ($field, $operator, $conditionValue) {
                return ($other->condition_field ?: null) === $field
                    && ($field === null || (
                        ($other->operator ?: 'eq') === $operator
                        && $this->sortedValues($other->condition_value) === $this->sortedValues($conditionValue)
                    ));
            });

        if ($sameCondition) {
            $validator->errors()->add('key', 'This policy already has a rule for this setting with the same condition. Edit that rule instead.');
        }
    }

    private function sortedValues(?string $values): string
    {
        $list = array_filter(array_map('trim', explode(',', (string) $values)), 'strlen');
        sort($list);

        return implode(',', $list);
    }
}
