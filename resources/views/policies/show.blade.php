@extends('layouts.master')

@section('title', $policy->title)

@php
$badgeClass = match($policy->status) {
    'active' => 'bg-success',
    'draft' => 'bg-secondary',
    'pending' => 'bg-warning text-dark',
    'approved' => 'bg-info text-dark',
    'archived' => 'bg-dark',
    'revoked' => 'bg-danger',
    default => 'bg-light text-dark'
};

// Platform defaults can be read by every university but changed only by
// super-admins; a university edits its own copy (see PolicyController::customize).
$canManage = $policy->isManageableBy(auth()->user());
@endphp

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'University Policies', 'url' => route('policies.index')],
        ['name' => $policy->title, 'url' => '#']
    ]" />

    <x-page-header title="POLICY DETAILS" description="{{ $policy->code }} — v{{ $policy->version }}" backUrl="{{ route('policies.index') }}">
        @if($canManage)
        @can('edit_policies')
            @if(in_array($policy->status, [App\Models\Policy::STATUS_DRAFT, App\Models\Policy::STATUS_PENDING, App\Models\Policy::STATUS_ARCHIVED, App\Models\Policy::STATUS_REVOKED]))
            <a href="{{ route('policies.edit', $policy) }}" class="btn btn-primary btn-sm rounded-sm">Edit</a>
            @endif
        @endcan
        @endif
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    @if($policy->isPlatform())
    <div class="alert alert-info py-2 px-3 rounded-sm mb-3" style="font-size: 0.72rem;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <i class="ti ti-world me-1"></i>
                <strong>Platform default.</strong>
                Every university follows this policy until it publishes one of its own. It can only be changed by a platform administrator.
                @if($ownCopy)
                    Your university already has its own copy
                    (<span class="badge {{ $ownCopy->isActive() ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($ownCopy->status) }}</span>).
                @endif
            </div>
            @if($ownCopy)
                <a href="{{ route('policies.show', $ownCopy) }}" class="btn btn-primary btn-sm rounded-sm">Open my university's copy</a>
            @elseif(auth()->user()->university_id)
                @can('create_policies')
                <form method="POST" action="{{ route('policies.customize', $policy) }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm">
                        <i class="ti ti-copy me-1"></i> Customize for my university
                    </button>
                </form>
                @endcan
            @endif
        </div>
    </div>
    @endif

    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="card rounded-sm text-center h-100" style="border-left: 3px solid #A3C4E8;">
                <div class="card-body py-2 px-2">
                    <div class="fw-bold" style="font-size: 1.2rem;">{{ $policy->categoryLabel() }}</div>
                    <div class="text-muted" style="font-size: 0.65rem;">Category</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card rounded-sm text-center h-100" style="border-left: 3px solid #A3C4E8;">
                <div class="card-body py-2 px-2">
                    <div class="fw-bold" style="font-size: 1.2rem;"><span class="badge {{ $badgeClass }}">{{ ucfirst($policy->status) }}</span></div>
                    <div class="text-muted" style="font-size: 0.65rem;">Status</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card rounded-sm text-center h-100" style="border-left: 3px solid #A3C4E8;">
                <div class="card-body py-2 px-2">
                    <div class="fw-bold" style="font-size: 1.2rem;">{{ $policy->effective_date?->format('d M Y') ?? '-' }}</div>
                    <div class="text-muted" style="font-size: 0.65rem;">Effective Date</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card rounded-sm text-center h-100" style="border-left: 3px solid #A3C4E8;">
                <div class="card-body py-2 px-2">
                    <div class="fw-bold" style="font-size: 1.2rem;">{{ $policy->review_due_date?->format('d M Y') ?? '-' }}</div>
                    <div class="text-muted" style="font-size: 0.65rem;">Review Due</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-2">
    @php
    $pages = [];
    $pageByNumber = [];
    foreach ($policy->sections ?? [] as $section) {
        if (empty($section['number']) && empty($section['title']) && empty($section['content'])) {
            continue;
        }
        $content = $section['content'] ?? '';
        $parts = preg_split('/\r?\n(?=\d+\.\d+(?:\.\d+)?\s+)/', $content, -1, PREG_SPLIT_NO_EMPTY) ?: [$content];
        $mainNumber = $section['number'] ?? '';
        $mainTitle = $section['title'] ?? '';
        $pages[] = [
            'number' => $mainNumber,
            'title' => $mainTitle,
            'content' => trim($parts[0] ?? ''),
            'is_main' => true,
        ];
        $pageByNumber[$mainNumber] = count($pages) - 1;
        for ($i = 1; $i < count($parts); $i++) {
            $part = trim($parts[$i]);
            if (preg_match('/^(\d+\.\d+(?:\.\d+)?)\s+([^\n]+)/', $part, $m)) {
                $subNumber = $m[1];
                $subTitle = trim($m[2]);
                $subContent = trim(substr($part, strlen($m[0])));
                $pages[] = [
                    'number' => $subNumber,
                    'title' => $subTitle,
                    'content' => $subContent,
                    'is_main' => false,
                ];
                $pageByNumber[$subNumber] = count($pages) - 1;
            }
        }
    }
    $currentNumber = request('page');
    $currentIndex = is_string($currentNumber) && isset($pageByNumber[$currentNumber]) ? $pageByNumber[$currentNumber] : 0;
    $currentPage = $pages[$currentIndex] ?? ['number' => '', 'title' => '', 'content' => '', 'is_main' => true];
    $totalPages = count($pages);
    $linkRegulations = function ($text) use ($pageByNumber, $currentNumber) {
        return preg_replace_callback('/\b(\d+\.\d+(?:\.\d+)*)\b/', function ($m) use ($pageByNumber, $currentNumber) {
            $n = $m[1];
            if (! isset($pageByNumber[$n])) {
                return $n;
            }
            if ((string) $currentNumber === (string) $n) {
                return '<span class="fw-semibold text-primary text-decoration-underline">'.$n.'</span>';
            }
            return '<a href="?page='.$n.'" class="fw-semibold text-primary text-decoration-none">'.$n.'</a>';
        }, $text);
    };
    @endphp
        <div class="col-lg-8">
            <div class="book-page p-4 p-md-5 mb-3" style="background: #fff; min-height: 70vh; border-radius: 0.5rem; border: 1px solid #e0e0e0; box-shadow: 0 8px 30px rgba(0,0,0,0.12);">
                <div class="mb-4 border-bottom pb-3 d-flex justify-content-between align-items-start">
                    <h1 class="fw-bold" style="font-size: 1.1rem; color: #0d6efd;">{{ $currentPage['number'] }} — {{ $currentPage['title'] }}</h1>
                    <span style="font-size: 0.65rem;" class="text-muted">Version {{ $policy->version }}</span>
                </div>
                <div style="font-size: 0.8rem; line-height: 1.75; white-space: normal;">
                    {!! nl2br($linkRegulations(e($currentPage['content']))) !!}
                </div>
                <div class="text-center text-muted mt-5 pt-3 border-top" style="font-size: 0.7rem;">
                    Page {{ $currentIndex + 1 }} of {{ $totalPages }}
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                @if($currentIndex > 0)
                <a href="?page={{ $pages[$currentIndex - 1]['number'] }}" class="btn btn-outline-secondary btn-sm rounded-sm">← Previous</a>
                @else
                <span></span>
                @endif
                @if($currentIndex < $totalPages - 1)
                <a href="?page={{ $pages[$currentIndex + 1]['number'] }}" class="btn btn-outline-secondary btn-sm rounded-sm">Next →</a>
                @endif
            </div>

            @if($policy->document_path)
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-paperclip me-1"></i> Attached Document</span>
                </div>
                <div class="card-body py-2 px-3" style="font-size: 0.75rem;">
                    <a href="{{ asset('storage/'.$policy->document_path) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-sm">
                        <i class="ti ti-download me-1"></i> Download Document
                    </a>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;">Contents</span>
                </div>
                <div class="list-group list-group-flush" style="max-height: 40vh; overflow-y: auto; font-size: 0.65rem;">
                    @foreach($pages as $idx => $p)
                    <a href="?page={{ $p['number'] }}" class="list-group-item list-group-item-action py-1 px-2 {{ $idx === $currentIndex ? 'active' : '' }}">
                        @if($p['is_main'])
                        <strong>{{ $p['number'] }} — {{ $p['title'] }}</strong>
                        @else
                        <span class="ms-2">{{ $p['number'] }} {{ $p['title'] }}</span>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>

            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;">Approval & Governance</span>
                </div>
                <div class="card-body py-2 px-3" style="font-size: 0.7rem;">
                    <p class="mb-1"><strong>Scope:</strong> {{ $policy->scopeLabel() }}</p>
                    <p class="mb-1"><strong>Scope ID:</strong> {{ $policy->scope_id ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Meeting Reference:</strong> {{ $policy->meeting_reference ?? '-' }}</p>
                    <p class="mb-1"><strong>Approved At:</strong> {{ $policy->approved_at?->format('d M Y H:i') ?? '-' }}</p>
                    <p class="mb-1"><strong>Approved By:</strong> {{ $policy->approver?->full_name ?? '-' }}</p>
                    <p class="mb-1"><strong>Created By:</strong> {{ $policy->creator?->full_name ?? '-' }}</p>
                    <p class="mb-0"><strong>Last Updated:</strong> {{ $policy->updated_at?->format('d M Y H:i') }}</p>
                </div>
            </div>

            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-list-check me-1"></i> Policy Rules</span>
                    @if($canManage)
                    @can('edit_policies')
                    <a href="{{ route('policies.rules.index', $policy) }}" class="btn btn-outline-primary btn-xs rounded-sm py-0 px-1">Manage</a>
                    @endcan
                    @endif
                </div>
                <div class="card-body py-2 px-3" style="font-size: 0.7rem;">
                    @if($policy->activeRules->isEmpty())
                    <p class="text-muted mb-0">No rules defined yet. Rules allow the PolicyEngine to return values based on context.</p>
                    @else
                    @php $ruleCatalog = app(\App\Services\PolicyCatalog::class); @endphp
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr><th>Setting</th><th>Value</th><th>Applies to</th></tr>
                            </thead>
                            <tbody>
                                {{-- Someone who can't open the rules editor (a platform default
                                     seen from a university) has nowhere to see the rest, so
                                     they get the complete list here instead of the first five. --}}
                                @foreach($policy->activeRules->take($canManage ? 5 : 200) as $rule)
                                <tr>
                                    <td>{{ $ruleCatalog->label($rule->key) }}</td>
                                    <td>{{ $ruleCatalog->describeValue($rule) }}</td>
                                    <td>{{ $ruleCatalog->describeCondition($rule) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($policy->activeRules->count() > 5 && $canManage)
                    <a href="{{ route('policies.rules.index', $policy) }}" class="small" style="font-size: 0.65rem;">View all rules</a>
                    @endif
                    @endif
                </div>
            </div>

            @if($policy->hasSignatures())
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;">Signatures</span>
                </div>
                <div class="card-body py-2 px-3" style="font-size: 0.7rem;">
                    <div class="mb-3">
                        <strong>Chairperson:</strong> {{ $policy->chairperson_name }}<br>
                        @if($policy->chairperson_signature_path)
                        <img src="{{ asset('storage/'.$policy->chairperson_signature_path) }}" alt="Chairperson signature" class="img-fluid border rounded-sm mt-1" style="max-height: 80px;">
                        @endif
                    </div>
                    <div>
                        <strong>Secretary:</strong> {{ $policy->secretary_name }}<br>
                        @if($policy->secretary_signature_path)
                        <img src="{{ asset('storage/'.$policy->secretary_signature_path) }}" alt="Secretary signature" class="img-fluid border rounded-sm mt-1" style="max-height: 80px;">
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;">Actions</span>
                </div>
                <div class="card-body py-2 px-3">
                    @if($canManage)
                    @can('approve_policies')
                        @if(in_array($policy->status, [App\Models\Policy::STATUS_DRAFT, App\Models\Policy::STATUS_PENDING]))
                        <form method="GET" action="{{ route('policies.approve', $policy) }}" class="mb-2">
                            <button type="submit" class="btn btn-success btn-sm w-100 rounded-sm">Approve & Sign</button>
                        </form>
                        @endif
                    @endcan

                    @can('edit_policies')
                        @if($policy->isDraft() || $policy->isRevoked())
                        <form method="POST" action="{{ route('policies.submit', $policy) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm w-100 rounded-sm">Submit for Approval</button>
                        </form>
                        @endif
                    @endcan

                    @can('approve_policies')
                        @if($policy->isApproved())
                        <form method="POST" action="{{ route('policies.publish', $policy) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm w-100 rounded-sm">Publish / Activate</button>
                        </form>
                        @endif
                    @endcan

                    @can('edit_policies')
                        @if($policy->isActive())
                        <form method="POST" action="{{ route('policies.archive', $policy) }}" class="mb-2" onsubmit="return confirm('Archive this policy?');">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm w-100 rounded-sm">Archive</button>
                        </form>
                        <form method="POST" action="{{ route('policies.revoke', $policy) }}" class="mb-2" onsubmit="return confirm('Revoke this policy?');">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm w-100 rounded-sm">Revoke</button>
                        </form>
                        @endif
                    @endcan
                    @endif

                    <a href="{{ route('policies.versions', $policy) }}" class="btn btn-outline-info btn-sm w-100 rounded-sm mb-2">Version History</a>

                    @if($canManage)
                    @can('delete_policies')
                    <form method="POST" action="{{ route('policies.destroy', $policy) }}" onsubmit="return confirm('Delete this policy? It will be archived.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-sm">Delete</button>
                    </form>
                    @endcan
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
