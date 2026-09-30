<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Policy extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'policies';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_ARCHIVED = 'archived';

    public const STATUS_REVOKED = 'revoked';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PENDING => 'Pending',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_ARCHIVED => 'Archived',
        self::STATUS_REVOKED => 'Revoked',
    ];

    public const CATEGORIES = [
        'handbook' => 'Handbook',
        'examination_regulation' => 'Examination Regulation',
        'hr_policy' => 'HR Policy',
        'academic_policy' => 'Academic Policy',
        'general' => 'General Policy',
    ];

    public const SCOPES = [
        'university-wide' => 'University-wide',
        'school' => 'School/Faculty',
        'department' => 'Department',
        'role' => 'Role',
    ];

    protected $fillable = [
        'university_id', 'code', 'title', 'category', 'scope', 'scope_id',
        'description', 'content', 'document_path', 'version', 'status',
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

    public static function boot()
    {
        parent::boot();

        static::creating(function ($policy) {
            if (empty($policy->status)) {
                $policy->status = self::STATUS_DRAFT;
            }
            if (empty($policy->version)) {
                $policy->version = 1;
            }
        });
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

    public function versions()
    {
        return $this->hasMany(PolicyVersion::class)->orderByDesc('version');
    }

    public function rules()
    {
        return $this->hasMany(PolicyRule::class)->orderByDesc('priority');
    }

    public function ruleChanges()
    {
        return $this->hasMany(PolicyRuleChange::class);
    }

    public function activeRules()
    {
        return $this->hasMany(PolicyRule::class)->where('is_active', true)->orderByDesc('priority');
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    /**
     * Policies a university may see and have applied to it: its own, plus
     * the platform-level ones (university_id IS NULL) that act as the
     * defaults for every university. Never another university's.
     */
    public function scopeVisibleTo($query, ?int $universityId)
    {
        return $query->where(function ($q) use ($universityId) {
            $q->whereNull($this->qualifyColumn('university_id'));

            if ($universityId !== null) {
                $q->orWhere($this->qualifyColumn('university_id'), $universityId);
            }
        });
    }

    /**
     * A platform policy is the default every university inherits until it
     * publishes a policy of its own - it belongs to no university.
     */
    public function isPlatform(): bool
    {
        return $this->university_id === null;
    }

    /**
     * Platform defaults are managed only by super-admins - a university's
     * own admins can read them and copy them (PolicyController::customize)
     * but never edit what every other university is also inheriting. A
     * university's own policies are managed by that university's users.
     */
    public function isManageableBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->hasRole('super-admin')) {
            return true;
        }

        return ! $this->isPlatform()
            && (int) $this->university_id === (int) $user->university_id;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Already in force: no effective date, or one that has arrived.
     */
    public function scopeEffective($query)
    {
        return $query->where(function ($q) {
            $q->whereNull($this->qualifyColumn('effective_date'))
                ->orWhereDate($this->qualifyColumn('effective_date'), '<=', now());
        });
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    public function isRevoked(): bool
    {
        return $this->status === self::STATUS_REVOKED;
    }

    public function canBePublished(): bool
    {
        return $this->isApproved()
            && $this->approved_at
            && $this->chairperson_signature_path
            && $this->secretary_signature_path;
    }

    public function hasSignatures(): bool
    {
        return ! empty($this->chairperson_signature_path) && ! empty($this->secretary_signature_path);
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category));
    }

    public function scopeLabel(): string
    {
        return self::SCOPES[$this->scope] ?? ucfirst($this->scope);
    }

    public function createVersion(): PolicyVersion
    {
        $attributes = $this->getAttributes();
        $excluded = ['id', 'created_at', 'updated_at', 'deleted_at'];
        $versionData = [];

        foreach ($attributes as $key => $value) {
            if (! in_array($key, $excluded)) {
                $versionData[$key] = $value;
            }
        }

        $versionData['sections'] = $this->sections;

        $versionData['version'] = $this->version ?: 1;

        return $this->versions()->create($versionData);
    }

    public function deleteDocument(): void
    {
        if ($this->document_path && Storage::disk('public')->exists($this->document_path)) {
            Storage::disk('public')->delete($this->document_path);
        }
    }

    public function deleteSignatures(): void
    {
        foreach ([$this->chairperson_signature_path, $this->secretary_signature_path] as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    protected static function booted(): void
    {
        static::deleting(function ($policy) {
            if (! $policy->isForceDeleting()) {
                return;
            }
            $policy->deleteDocument();
            $policy->deleteSignatures();
            $policy->versions()->delete();
        });
    }
}
