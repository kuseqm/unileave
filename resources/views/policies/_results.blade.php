<div class="card rounded-sm" id="policiesResults">
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0" style="font-size: 0.75rem;">
            <thead class="table-light">
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Scope</th>
                    <th>Version</th>
                    <th>Status</th>
                    <th>Effective</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($policies as $policy)
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
                @endphp
                <tr>
                    <td>
                        {{ $policy->code }}
                        @if($policy->isPlatform())
                            <span class="badge bg-info text-dark ms-1" title="Inherited by every university until it publishes its own">Platform default</span>
                        @endif
                    </td>
                    <td><a href="{{ route('policies.show', $policy) }}">{{ $policy->title }}</a></td>
                    <td>{{ $policy->categoryLabel() }}</td>
                    <td>{{ $policy->scopeLabel() }}</td>
                    <td>v{{ $policy->version }}</td>
                    <td><span class="badge {{ $badgeClass }}">{{ ucfirst($policy->status) }}</span></td>
                    <td>{{ $policy->effective_date?->format('d M Y') ?? '-' }}</td>
                    <td class="text-end">
                        <a href="{{ route('policies.show', $policy) }}" class="btn btn-outline-primary btn-xs rounded-sm py-0 px-1">View</a>
                        @if($policy->isManageableBy(auth()->user()))
                        @can('edit_policies')
                            @if(in_array($policy->status, [App\Models\Policy::STATUS_DRAFT, App\Models\Policy::STATUS_PENDING, App\Models\Policy::STATUS_ARCHIVED, App\Models\Policy::STATUS_REVOKED]))
                            <a href="{{ route('policies.edit', $policy) }}" class="btn btn-outline-secondary btn-xs rounded-sm py-0 px-1">Edit</a>
                            @endif
                        @endcan
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">No policies found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-2">{{ $policies->links() }}</div>
</div>
