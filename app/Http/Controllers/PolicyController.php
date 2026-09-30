<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PolicyController extends Controller
{
    use PerPageTrait;

    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request);

        $query = $this->visiblePolicies()
            ->with(['creator', 'approver', 'university'])
            ->orderByDesc('updated_at');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        $policies = $query->paginate($perPage)->appends($request->query());

        $stats = [
            'total' => $this->visiblePolicies()->count(),
            'active' => $this->visiblePolicies()->where('status', Policy::STATUS_ACTIVE)->count(),
            'approved' => $this->visiblePolicies()->where('status', Policy::STATUS_APPROVED)->count(),
            'pending' => $this->visiblePolicies()->where('status', Policy::STATUS_PENDING)->count(),
            'draft' => $this->visiblePolicies()->where('status', Policy::STATUS_DRAFT)->count(),
            'archived' => $this->visiblePolicies()->whereIn('status', [Policy::STATUS_ARCHIVED, Policy::STATUS_REVOKED])->count(),
        ];

        $categories = Policy::CATEGORIES;
        $statuses = Policy::STATUSES;

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('policies._results', compact('policies'))->render(),
                'total' => $policies->total(),
                'grand_total' => $stats['total'],
            ]);
        }

        return view('policies.index', compact('policies', 'stats', 'categories', 'statuses'));
    }

    public function create()
    {
        return view('policies.create');
    }

    public function store(Request $request)
    {
        $universityId = $this->targetUniversityId($request);
        $validated = $this->validatePolicy($request, null, $universityId);

        $policy = DB::transaction(function () use ($request, $validated, $universityId) {
            $policy = new Policy($validated);
            $policy->status = $request->input('status', Policy::STATUS_DRAFT);
            $policy->version = 1;
            $policy->created_by = auth()->id();
            $policy->university_id = $universityId;

            if ($request->hasFile('document')) {
                $policy->document_path = $request->file('document')->store('policies/documents', 'public');
            }

            $policy->save();

            return $policy;
        });

        return redirect()->route('policies.show', $policy)
            ->with('success', 'Policy created successfully.');
    }

    public function show(Policy $policy)
    {
        $policy->load(['creator', 'approver', 'updater', 'activeRules', 'versions' => function ($q) {
            $q->take(10);
        }]);

        // For a platform default: the reader's own university's copy of it, if
        // there is one, so the page can point to it instead of offering a second copy.
        $ownCopy = $policy->isPlatform() && auth()->user()->university_id
            ? Policy::where('university_id', auth()->user()->university_id)->where('code', $policy->code)->first()
            : null;

        return view('policies.show', compact('policy', 'ownCopy'));
    }

    public function edit(Policy $policy)
    {
        return view('policies.edit', compact('policy'));
    }

    public function update(Request $request, Policy $policy)
    {
        $validated = $this->validatePolicy($request, $policy, $policy->university_id);

        DB::transaction(function () use ($request, $validated, $policy) {
            if ($this->shouldCreateVersion($policy, $validated, $request)) {
                $policy->createVersion();
                $policy->version += 1;
            }

            $policy->fill($validated);
            $policy->updated_by = auth()->id();

            if ($request->hasFile('document')) {
                if ($policy->document_path) {
                    Storage::disk('public')->delete($policy->document_path);
                }
                $policy->document_path = $request->file('document')->store('policies/documents', 'public');
            }

            if ($request->boolean('remove_document') && $policy->document_path) {
                Storage::disk('public')->delete($policy->document_path);
                $policy->document_path = null;
            }

            $policy->save();
        });

        return redirect()->route('policies.show', $policy)
            ->with('success', 'Policy updated successfully.');
    }

    public function destroy(Policy $policy)
    {
        $policy->delete();

        return redirect()->route('policies.index')
            ->with('success', 'Policy archived successfully.');
    }

    public function approve(Policy $policy)
    {
        return view('policies.approve', compact('policy'));
    }

    public function storeApproval(Request $request, Policy $policy)
    {
        $validated = $request->validate([
            'meeting_reference' => 'required|string|max:255',
            'effective_date' => 'required|date',
            'review_due_date' => 'nullable|date|after_or_equal:effective_date',
            'chairperson_name' => 'required|string|max:255',
            'chairperson_signature_data' => 'required|string',
            'secretary_name' => 'required|string|max:255',
            'secretary_signature_data' => 'required|string',
        ]);

        DB::transaction(function () use ($validated, $request, $policy) {
            $policy->createVersion();

            $chairPath = $this->storeSignature($request->input('chairperson_signature_data'), 'chairperson', $policy->id);
            $secretaryPath = $this->storeSignature($request->input('secretary_signature_data'), 'secretary', $policy->id);

            $policy->meeting_reference = $validated['meeting_reference'];
            $policy->effective_date = $validated['effective_date'];
            $policy->review_due_date = $validated['review_due_date'] ?? null;
            $policy->chairperson_name = $validated['chairperson_name'];
            $policy->chairperson_signature_path = $chairPath;
            $policy->secretary_name = $validated['secretary_name'];
            $policy->secretary_signature_path = $secretaryPath;
            $policy->approved_at = now();
            $policy->approved_by = auth()->id();
            $policy->updated_by = auth()->id();
            $policy->version += 1;
            $policy->status = Policy::STATUS_APPROVED;

            $policy->save();
        });

        return redirect()->route('policies.show', $policy)
            ->with('success', 'Policy approved and signatures captured. Publish when effective.');
    }

    public function publish(Policy $policy)
    {
        if (! $policy->canBePublished()) {
            return redirect()->route('policies.show', $policy)
                ->with('error', 'Policy must be approved and have both chairperson and secretary signatures before publishing.');
        }

        $policy->status = Policy::STATUS_ACTIVE;
        $policy->updated_by = auth()->id();
        $policy->save();

        return redirect()->route('policies.show', $policy)
            ->with('success', 'Policy is now active and effective.');
    }

    public function submitForApproval(Policy $policy)
    {
        if (! in_array($policy->status, [Policy::STATUS_DRAFT, Policy::STATUS_REVOKED])) {
            return redirect()->route('policies.show', $policy)
                ->with('error', 'Only draft or revoked policies can be submitted for approval.');
        }

        $policy->status = Policy::STATUS_PENDING;
        $policy->updated_by = auth()->id();
        $policy->save();

        return redirect()->route('policies.show', $policy)
            ->with('success', 'Policy submitted for approval.');
    }

    public function archive(Policy $policy)
    {
        $policy->createVersion();
        $policy->status = Policy::STATUS_ARCHIVED;
        $policy->updated_by = auth()->id();
        $policy->save();

        return redirect()->route('policies.show', $policy)
            ->with('success', 'Policy archived successfully.');
    }

    public function revoke(Policy $policy)
    {
        $policy->createVersion();
        $policy->status = Policy::STATUS_REVOKED;
        $policy->updated_by = auth()->id();
        $policy->save();

        return redirect()->route('policies.show', $policy)
            ->with('success', 'Policy revoked successfully.');
    }

    public function versions(Policy $policy)
    {
        $versions = $policy->versions()->with('creator')->paginate(10);

        return view('policies.versions', compact('policy', 'versions'));
    }

    /**
     * Give a platform default's owner-less policy its own editable copy for
     * the signed-in user's university: same content and rules, but a fresh
     * draft that has to go through this university's own approval and
     * signatures before it takes effect. Until it's published the platform
     * default keeps applying, so nothing changes by accident.
     */
    public function customize(Policy $policy)
    {
        $user = auth()->user();

        abort_unless($policy->isPlatform() && $user->university_id, 403, 'Only a platform default policy can be copied to a university.');

        // Trashed copies count too: (university_id, code) is unique across
        // soft-deleted rows, so a second copy could never be inserted next to
        // one that was deleted. Bring the deleted one back instead - it still
        // holds that university's own edits.
        $existing = Policy::withTrashed()
            ->where('university_id', $user->university_id)
            ->where('code', $policy->code)
            ->first();

        if ($existing) {
            $wasDeleted = $existing->trashed();

            if ($wasDeleted) {
                $existing->restore();
            }

            return redirect()->route('policies.show', $existing)
                ->with('success', $wasDeleted
                    ? "Your university's earlier copy of this policy was restored."
                    : 'Your university already has its own copy of this policy - opened it for you.');
        }

        $copy = DB::transaction(function () use ($policy, $user) {
            $copy = $policy->replicate([
                'status', 'version', 'university_id', 'document_path', 'effective_date', 'review_due_date',
                'approved_at', 'approved_by', 'meeting_reference', 'chairperson_name',
                'chairperson_signature_path', 'secretary_name', 'secretary_signature_path',
            ]);
            $copy->university_id = $user->university_id;
            $copy->status = Policy::STATUS_DRAFT;
            $copy->version = 1;
            $copy->created_by = $user->id;
            $copy->updated_by = null;
            $copy->save();

            foreach ($policy->rules as $rule) {
                $ruleCopy = $rule->replicate(['policy_id', 'created_by', 'updated_by']);
                $ruleCopy->policy_id = $copy->id;
                $ruleCopy->created_by = $user->id;
                $ruleCopy->save();
            }

            return $copy;
        });

        return redirect()->route('policies.show', $copy)
            ->with('success', 'A draft copy was created for your university. Adjust its rules, then submit it for approval - the platform default keeps applying until yours is published.');
    }

    /**
     * Policies the signed-in user can see: their university's own, plus the
     * platform defaults.
     */
    private function visiblePolicies()
    {
        return Policy::visibleTo(auth()->user()->university_id);
    }

    /**
     * Which university a newly created policy belongs to. A super-admin may
     * create a platform default (no university) by ticking "is_platform";
     * everyone else always creates for their own university, and a user with
     * no university can't create policies at all.
     */
    private function targetUniversityId(Request $request): ?int
    {
        $user = auth()->user();

        if ($user->hasRole('super-admin') && $request->boolean('is_platform')) {
            return null;
        }

        abort_unless($user->university_id, 403, 'Your account is not attached to a university.');

        return (int) $user->university_id;
    }

    private function validatePolicy(Request $request, ?Policy $policy = null, ?int $universityId = null): array
    {
        $rules = [
            // Unique within the university (or within the platform layer),
            // not globally - each university may hold its own copy of a code.
            'code' => [
                'required', 'string', 'max:100',
                Rule::unique('policies', 'code')
                    ->where(fn ($query) => $universityId === null
                        ? $query->whereNull('university_id')
                        : $query->where('university_id', $universityId))
                    ->ignore($policy?->id),
            ],
            'title' => 'required|string|max:255',
            'category' => ['required', Rule::in(array_keys(Policy::CATEGORIES))],
            'scope' => ['required', Rule::in(array_keys(Policy::SCOPES))],
            'scope_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'status' => ['nullable', Rule::in([Policy::STATUS_DRAFT, Policy::STATUS_PENDING])],
            'effective_date' => 'nullable|date',
            'review_due_date' => 'nullable|date|after_or_equal:effective_date',
            'sections' => 'nullable|array',
            'sections.*.number' => 'nullable|string|max:50',
            'sections.*.title' => 'nullable|string|max:255',
            'sections.*.content' => 'nullable|string',
        ];

        return $request->validate($rules);
    }

    private function shouldCreateVersion(Policy $policy, array $validated, Request $request): bool
    {
        if ($request->hasFile('document')) {
            return true;
        }

        $trackedFields = ['title', 'category', 'scope', 'scope_id', 'description', 'content', 'effective_date', 'review_due_date', 'sections'];

        foreach ($trackedFields as $field) {
            if ($field === 'sections') {
                $old = json_encode($policy->{$field} ?? []);
                $new = json_encode($validated[$field] ?? []);
            } else {
                $old = (string) $policy->{$field};
                $new = (string) ($validated[$field] ?? '');
            }
            if ($old !== $new) {
                return true;
            }
        }

        return false;
    }

    public function publicShow($code)
    {
        // The reader's own university's version if it has one, otherwise the
        // platform default - never another university's.
        $policy = $this->visiblePolicies()
            ->where('code', $code)
            ->whereIn('status', [Policy::STATUS_ACTIVE, Policy::STATUS_APPROVED])
            ->orderByRaw('policies.university_id is null')
            ->firstOrFail();

        return view('policies.public', compact('policy'));
    }

    private function storeSignature(string $data, string $prefix, int $policyId): string
    {
        $payload = $data;
        if (str_contains($payload, ',')) {
            $payload = explode(',', $payload)[1];
        }

        $path = "policies/signatures/{$prefix}_{$policyId}_".uniqid().'.png';
        Storage::disk('public')->put($path, base64_decode($payload));

        return $path;
    }
}
