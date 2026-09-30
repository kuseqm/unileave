@extends('layouts.master')

@section('title', 'Policy Rules')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'University Policies', 'url' => route('policies.index')],
        ['name' => $policy->title, 'url' => route('policies.show', $policy)],
        ['name' => 'Rules', 'url' => '#']
    ]" />

    <x-page-header title="POLICY RULES" description="Rules that the PolicyEngine will evaluate for {{ $policy->code }}" backUrl="{{ route('policies.show', $policy) }}">
        <a href="{{ route('policies.changes', $policy) }}" class="btn btn-outline-secondary btn-sm rounded-sm"><i class="ti ti-history me-1"></i> Change history</a>
        @can('edit_policies')
        <a href="{{ route('policies.rules.create', $policy) }}" class="btn btn-primary btn-sm rounded-sm"><i class="ti ti-plus me-1"></i> Add Rule</a>
        @endcan
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2">
        <!-- Rules List - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" style="font-size: 0.75rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Setting</th>
                                <th>Value</th>
                                <th>Applies to</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rules as $rule)
                            @php $enforcement = $rule->catalogDefinition()['enforcement'] ?? null; @endphp
                            <tr>
                                <td>
                                    {{ $catalog->label($rule->key) }}
                                    <div class="text-muted" style="font-size: 0.6rem;">{{ $rule->key }}</div>
                                </td>
                                <td>{{ $catalog->describeValue($rule) }}</td>
                                <td>{{ $catalog->describeCondition($rule) }}</td>
                                <td>{{ $rule->priority }}</td>
                                <td>
                                    <span class="badge {{ $rule->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $rule->is_active ? 'Active' : 'Inactive' }}</span>
                                    @if($enforcement)
                                        <span class="badge {{ match($enforcement) { 'enforced' => 'bg-success', 'display' => 'bg-info text-dark', 'planned' => 'bg-warning text-dark', default => 'bg-secondary' } }}"
                                              title="{{ $catalog->definition($rule->key)['enforced_by'] ?? '' }}">{{ $catalog->enforcementLabel($rule->key) }}</span>
                                    @else
                                        <span class="badge bg-danger" title="The system no longer recognises this setting - it does nothing. Delete it.">Unknown setting</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @can('edit_policies')
                                    <a href="{{ route('policies.rules.edit', [$policy, $rule]) }}" class="btn btn-outline-secondary btn-xs rounded-sm py-0 px-1">Edit</a>
                                    @endcan
                                    @can('delete_policies')
                                    {{-- A rule of a policy in force takes its reason with it: no reason, no delete. --}}
                                    <form method="POST" action="{{ route('policies.rules.destroy', [$policy, $rule]) }}" class="d-inline"
                                          onsubmit="{{ $policy->isActive()
                                              ? "const why = prompt('This policy is in force and the change applies at once. Why is this rule being deleted?'); if (!why || why.trim().length < 5) { return false; } this.reason.value = why.trim(); return true;"
                                              : "return confirm('Delete this rule?');" }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="reason" value="">

                                        <button type="submit" class="btn btn-outline-danger btn-xs rounded-sm py-0 px-1">Delete</button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No rules defined for this policy.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-2">{{ $rules->links() }}</div>
            </div>
        </div>

        <!-- Information - narrow column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> About Rules</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>Pick a <strong>Setting</strong> from the list - the system only reads the settings it knows</li>
                            <li>A rule with an <strong>Applies to</strong> condition beats an "Everyone" rule for the same setting</li>
                            <li>The badge beside each rule says whether the system already <strong>enforces</strong> it</li>
                            <li><span class="badge bg-secondary">Inactive</span> rules are skipped entirely</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
