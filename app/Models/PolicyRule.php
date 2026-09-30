<?php

namespace App\Models;

use App\Services\PolicyCatalog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyRule extends Model
{
    use HasFactory;

    protected $table = 'policy_rules';

    public const TYPES = [
        'string' => 'String',
        'numeric' => 'Numeric',
        'boolean' => 'Boolean',
        'date' => 'Date',
        'json' => 'JSON',
    ];

    public const OPERATORS = [
        'eq' => 'Equals',
        'neq' => 'Not equals',
        'gt' => 'Greater than',
        'gte' => 'Greater than or equal',
        'lt' => 'Less than',
        'lte' => 'Less than or equal',
        'in' => 'In list',
        'between' => 'Between',
    ];

    protected $fillable = [
        'policy_id', 'key', 'label', 'value', 'type', 'operator',
        'condition_field', 'condition_value', 'priority', 'is_active',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
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

    /**
     * The catalog entry for this rule's setting, or null when the system no
     * longer recognises the key.
     *
     * @return array<string, mixed>|null
     */
    public function catalogDefinition(): ?array
    {
        return app(PolicyCatalog::class)->definition($this->key);
    }

    public function typedValue()
    {
        $value = $this->value;

        switch ($this->type) {
            case 'numeric':
                return is_numeric($value) ? $value + 0 : $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'date':
                return $value ? date('Y-m-d', strtotime($value)) : null;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    public function matches(array $context = []): bool
    {
        if (empty($this->condition_field)) {
            return true;
        }

        $actual = $context[$this->condition_field] ?? null;
        $expected = $this->condition_value;

        return match ($this->operator) {
            'neq' => (string) $actual !== (string) $expected,
            'gt' => is_numeric($actual) && is_numeric($expected) && $actual > $expected,
            'gte' => is_numeric($actual) && is_numeric($expected) && $actual >= $expected,
            'lt' => is_numeric($actual) && is_numeric($expected) && $actual < $expected,
            'lte' => is_numeric($actual) && is_numeric($expected) && $actual <= $expected,
            'in' => in_array((string) $actual, array_map('trim', explode(',', (string) $expected))),
            'between' => is_numeric($actual) && $this->isBetween($actual, (string) $expected),
            default => (string) $actual === (string) $expected,
        };
    }

    protected function isBetween($value, string $range): bool
    {
        $parts = array_map('trim', explode(',', $range));
        if (count($parts) !== 2) {
            return false;
        }

        [$min, $max] = $parts;

        return is_numeric($min) && is_numeric($max) && $value >= $min && $value <= $max;
    }
}
