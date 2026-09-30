<?php

namespace App\Services;

use App\Models\LeaveType;
use App\Models\PolicyRule;

/**
 * Read access to config/policy_catalog.php: which settings a policy rule may
 * control, what values and conditions each accepts, and what applies when no
 * rule matches. See that file for the shape of a definition.
 */
class PolicyCatalog
{
    public const ENFORCEMENT_LABELS = [
        'enforced' => 'Enforced',
        'display' => 'Shown to staff only',
        'planned' => 'Not enforced yet',
        'reference' => 'Reference only',
    ];

    /** Operators a rule may use. Numeric comparison ones make no sense for the current condition fields. */
    public const OPERATORS = [
        'eq' => 'is',
        'neq' => 'is not',
        'in' => 'is any of',
    ];

    /**
     * Every setting, or just those belonging to one policy category.
     *
     * @return array<string, array<string, mixed>>
     */
    public function keys(?string $category = null): array
    {
        $keys = config('policy_catalog.keys', []);

        if ($category === null) {
            return $keys;
        }

        return array_filter($keys, fn (array $definition) => $definition['category'] === $category);
    }

    /**
     * Settings grouped under their headings, for a category - the order the
     * rule editor lists them.
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function grouped(string $category): array
    {
        $grouped = [];

        foreach ($this->keys($category) as $key => $definition) {
            $grouped[$definition['group'] ?? 'General'][$key] = $definition;
        }

        return $grouped;
    }

    public function definition(string $key): ?array
    {
        // Indexed directly, not via a dotted config() path, so a key such as
        // "max_annual_leave.label" can't reach into a definition.
        return config('policy_catalog.keys', [])[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return $this->definition($key) !== null;
    }

    /**
     * What applies when no policy rule matches. Null means "no limit / not set".
     */
    public function default(string $key): mixed
    {
        return $this->definition($key)['default'] ?? null;
    }

    /**
     * The condition fields a setting's rules may use, with their labels and
     * the values each accepts.
     *
     * @return array<string, array{label: string, options: array<string, string>}>
     */
    public function conditionFields(string $key): array
    {
        $fields = [];

        foreach ($this->definition($key)['conditions'] ?? [] as $field) {
            $fields[$field] = [
                'label' => $this->conditionDefinition($field)['label'] ?? $field,
                'options' => $this->conditionOptions($field),
            ];
        }

        return $fields;
    }

    private function conditionDefinition(string $field): ?array
    {
        return config('policy_catalog.conditions', [])[$field] ?? null;
    }

    /**
     * @return array<string, string> value => label
     */
    public function conditionOptions(string $field): array
    {
        $options = $this->conditionDefinition($field)['options'] ?? [];

        if ($options === 'leave_types') {
            return LeaveType::orderBy('sort_order')->orderBy('id')->pluck('name', 'code')->all();
        }

        return $options;
    }

    /**
     * The whole catalog for one category in a form the rule editor's script
     * can use directly.
     *
     * @return array<string, array<string, mixed>>
     */
    public function forEditor(string $category): array
    {
        $editor = [];

        foreach ($this->keys($category) as $key => $definition) {
            $editor[$key] = [
                'label' => $definition['label'],
                'description' => $definition['description'] ?? null,
                'type' => $definition['type'],
                'unit' => $definition['unit'] ?? null,
                'min' => $definition['min'] ?? null,
                'max' => $definition['max'] ?? null,
                'integer' => $definition['integer'] ?? false,
                'options' => $definition['options'] ?? null,
                'default' => $definition['default'] ?? null,
                'enforcement' => $definition['enforcement'],
                'enforcementLabel' => self::ENFORCEMENT_LABELS[$definition['enforcement']],
                'enforcedBy' => $definition['enforced_by'] ?? null,
                'conditions' => $this->conditionFields($key),
            ];
        }

        return $editor;
    }

    /**
     * The column type a rule stores for a setting.
     */
    public function storedType(string $key): string
    {
        return match ($this->definition($key)['type'] ?? 'string') {
            'numeric' => 'numeric',
            'boolean' => 'boolean',
            default => 'string',
        };
    }

    public function enforcementLabel(string $key): ?string
    {
        $enforcement = $this->definition($key)['enforcement'] ?? null;

        return $enforcement ? self::ENFORCEMENT_LABELS[$enforcement] : null;
    }

    public function label(string $key): string
    {
        return $this->definition($key)['label'] ?? $key;
    }

    public function unit(string $key): ?string
    {
        return $this->definition($key)['unit'] ?? null;
    }

    /**
     * A rule's value as HR should read it: "45 days per year", "Yes",
     * "System sets it (day after leave ends)".
     */
    public function describeValue(PolicyRule $rule): string
    {
        return $this->describe($rule->key, $rule->value);
    }

    /**
     * Any value of a setting in words, whether it came from a rule or is the
     * setting's default. No value at all means the setting isn't limited.
     */
    public function describe(string $key, mixed $value): string
    {
        $definition = $this->definition($key);

        if ($value === null || $value === '') {
            // Some settings, left unset, are not "no limit" but something
            // more specific - the catalog says what.
            return $definition['default_label'] ?? 'Not set (no limit)';
        }

        if (! $definition) {
            return (string) $value;
        }

        return match ($definition['type']) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'Yes' : 'No',
            'select' => $definition['options'][$value] ?? (string) $value,
            'numeric' => $value.(isset($definition['unit']) ? ' '.$definition['unit'] : ''),
            default => (string) $value,
        };
    }

    /**
     * When a rule applies, in words: "Everyone", "Staff category is Academic",
     * "Leave type is any of Sick Leave, Compassionate Leave".
     */
    public function describeCondition(PolicyRule $rule): string
    {
        if (empty($rule->condition_field)) {
            return 'Everyone';
        }

        $field = $this->conditionDefinition($rule->condition_field);
        $options = $field ? $this->conditionOptions($rule->condition_field) : [];

        $values = array_map('trim', explode(',', (string) $rule->condition_value));
        $names = array_map(fn ($value) => $options[$value] ?? $value, $values);

        return sprintf(
            '%s %s %s',
            $field['label'] ?? $rule->condition_field,
            self::OPERATORS[$rule->operator ?: 'eq'] ?? $rule->operator,
            implode(', ', $names)
        );
    }
}
