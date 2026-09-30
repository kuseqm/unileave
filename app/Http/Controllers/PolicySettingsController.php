<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use App\Models\PolicyRule;
use App\Models\PolicyRuleChange;
use App\Services\PolicyCatalog;
use App\Services\PolicyEngine;
use Illuminate\Http\Request;

/**
 * The settings screen: every setting the system reads, the value in force for
 * the signed-in user's university, and where that value comes from - the
 * university's own policy, the platform default, or the built-in default.
 *
 * HR can also check what applies to a particular kind of staff member (say an
 * academic on annual leave), and jump straight to changing a setting.
 */
class PolicySettingsController extends Controller
{
    /** The condition fields HR can pick to see how a setting behaves for someone. */
    private const CONTEXT_FIELDS = ['staff_category', 'contract_type', 'leave_type'];

    public function __construct(private PolicyEngine $engine, private PolicyCatalog $catalog) {}

    public function index(Request $request)
    {
        $user = auth()->user();

        $categories = $this->categoriesWithSettings();
        $category = array_key_exists($request->query('category', ''), $categories) ? $request->query('category') : 'hr_policy';

        // Only values the catalog knows are accepted; anything else is ignored
        // rather than shown as a filter that quietly matches nothing.
        $filters = [];
        foreach (self::CONTEXT_FIELDS as $field) {
            $value = $request->query($field);

            if (filled($value) && array_key_exists($value, $this->catalog->conditionOptions($field))) {
                $filters[$field] = $value;
            }
        }

        $context = ['university_id' => $user->university_id] + $filters;

        $ownPolicy = $this->ownPolicy($user->university_id, $category);
        $platformPolicy = Policy::whereNull('university_id')->where('category', $category)->active()->orderBy('id')->first();
        $canChange = (bool) ($user->can('edit_policies') && $ownPolicy?->isManageableBy($user));

        $groups = [];
        foreach ($this->catalog->grouped($category) as $group => $settings) {
            foreach ($settings as $key => $definition) {
                $groups[$group][] = $this->row($key, $definition, $context, $category, $ownPolicy, $canChange);
            }
        }

        $recentChanges = PolicyRuleChange::visibleTo($user->university_id)
            ->with(['changer', 'policy'])
            ->latest('id')
            ->limit(8)
            ->get();

        return view('policies.settings', [
            'catalog' => $this->catalog,
            'categories' => $categories,
            'category' => $category,
            'filters' => $filters,
            'contextFields' => collect(self::CONTEXT_FIELDS)->mapWithKeys(fn (string $field) => [
                $field => [
                    'label' => config("policy_catalog.conditions.{$field}.label"),
                    'options' => $this->catalog->conditionOptions($field),
                ],
            ])->all(),
            'groups' => $groups,
            'ownPolicy' => $ownPolicy,
            'platformPolicy' => $platformPolicy,
            'canChange' => $canChange,
            'recentChanges' => $recentChanges,
        ]);
    }

    /**
     * Policy categories that have at least one setting the system knows.
     *
     * @return array<string, string>
     */
    private function categoriesWithSettings(): array
    {
        return collect(Policy::CATEGORIES)
            ->filter(fn (string $label, string $category) => $this->catalog->keys($category) !== [])
            ->all();
    }

    /**
     * The policy of this category the university keeps its own settings in:
     * the one in force if it has one, otherwise its first.
     */
    private function ownPolicy(?int $universityId, string $category): ?Policy
    {
        return Policy::where('university_id', $universityId)
            ->where('category', $category)
            ->orderByRaw('status = ? desc', [Policy::STATUS_ACTIVE])
            ->orderBy('id')
            ->first();
    }

    /**
     * One setting as the screen shows it.
     *
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    private function row(string $key, array $definition, array $context, string $category, ?Policy $ownPolicy, bool $canChange): array
    {
        $resolved = $this->engine->resolve($key, $context, null, $category);
        $usedRuleId = $resolved['rule']?->id;

        $rules = collect($this->engine->applicableRules($key, $context, $category))
            ->map(fn (array $entry) => [
                'source' => $entry['source'],
                'policy' => $entry['rule']->policy->code,
                'value' => $this->catalog->describeValue($entry['rule']),
                'applies_to' => $this->catalog->describeCondition($entry['rule']),
                'in_force' => $entry['rule']->id === $usedRuleId,
            ])
            ->all();

        return [
            'key' => $key,
            'label' => $definition['label'],
            'description' => $definition['description'] ?? null,
            'value' => $this->catalog->describe($key, $resolved['value']),
            'source' => $resolved['source'],
            'policy' => $resolved['rule']?->policy->code,
            'enforcement' => $definition['enforcement'],
            'enforcement_label' => $this->catalog->enforcementLabel($key),
            'rules' => $rules,
            'action' => $canChange ? $this->action($key, $resolved['rule'], $ownPolicy) : null,
        ];
    }

    /**
     * What "Change" does for a setting: edit the rule that's in force if it's
     * the university's own, otherwise add the university's own rule for it.
     *
     * @return array{label: string, url: string}
     */
    private function action(string $key, ?PolicyRule $inForce, Policy $ownPolicy): array
    {
        if ($inForce && $inForce->policy_id === $ownPolicy->id) {
            return ['label' => 'Edit', 'url' => route('policies.rules.edit', [$ownPolicy, $inForce])];
        }

        return ['label' => 'Set for my university', 'url' => route('policies.rules.create', [$ownPolicy, 'key' => $key])];
    }
}
