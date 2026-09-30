    <!-- Audit Trail Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-history text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Activity Log</span>
                    <span class="text-muted ms-2 small">Showing {{ $audits->firstItem() ?? 0 }} to {{ $audits->lastItem() ?? 0 }} of {{ $audits->total() }}</span>
                </div>
                <div class="text-muted small">
                    <i class="ti ti-shield me-1"></i> Non-repudiation record
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;" id="auditTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">#</th>
                            <th class="py-1">Date/Time</th>
                            <th class="py-1">User</th>
                            <th class="py-1">Role</th>
                            <th class="py-1">Entity</th>
                            <th class="py-1">Event</th>
                            <th class="py-1">Action</th>
                            <th class="py-1">IP Address</th>
                            <th class="pe-2 py-1" width="50">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                        <tr>
                            <td class="ps-2 py-1">{{ $audit->id }}</td>
                            <td class="py-1">
                                {{ $audit->created_at->format('d M Y, H:i:s') }}
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $audit->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-1">
                                {{ $audit->user->first_name ?? 'System' }} {{ $audit->user->last_name ?? '' }}
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $audit->user->email ?? '' }}</div>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-secondary rounded-sm" style="font-size: 0.6rem;">{{ $audit->user_role ?? 'Unknown' }}</span>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-info rounded-sm" style="font-size: 0.6rem;">{{ $audit->entity_type }}</span>
                                <div class="text-muted" style="font-size: 0.6rem;">ID: {{ $audit->auditable_id }}</div>
                            </td>
                            <td class="py-1">
                                {!! $audit->event_badge !!}
                            </td>
                            <td class="py-1">{{ Str::limit($audit->action, 50) }}</td>
                            <td class="py-1"><code style="font-size: 0.6rem;">{{ $audit->ip_address ?? 'N/A' }}</code></td>
                            <td class="pe-2 py-1">
                                <a href="{{ route('audit-trail.show', $audit->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="View Details">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                <i class="ti ti-history" style="font-size: 48px;"></i>
                                <p class="mt-2">No audit records found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <x-pagination :items="$audits" :perPageOptions="[10, 25, 50, 100]" :ajax="true" />
        </div>
    </div>
