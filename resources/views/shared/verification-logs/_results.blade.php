    <!-- Verification Log Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-scan text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">{{ ($isArchivedView ?? false) ? 'Archived Scans' : 'Scan Activity' }}</span>
                    <span class="text-muted ms-2 small">Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }}</span>
                </div>
                <div class="text-muted small">
                    <i class="ti ti-shield-check me-1"></i> Kept permanently, never deleted
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;" id="verificationLogTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">#</th>
                            <th class="py-1">Date/Time</th>
                            <th class="py-1">Document</th>
                            <th class="py-1">Outcome</th>
                            <th class="py-1">IP Address</th>
                            <th class="pe-2 py-1 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-2 py-1">{{ $log->id }}</td>
                            <td class="py-1">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-1">
                                <span class="badge bg-info rounded-sm" style="font-size: 0.6rem;">{{ str_replace('_', ' ', ucfirst($log->document_type)) }}</span>
                            </td>
                            <td class="py-1">
                                @if($log->successful)
                                    <span class="badge bg-success rounded-sm" style="font-size: 0.6rem;"><i class="ti ti-check"></i> Verified</span>
                                @else
                                    <span class="badge bg-danger rounded-sm" style="font-size: 0.6rem;"><i class="ti ti-x"></i> Failed</span>
                                @endif
                            </td>
                            <td class="py-1"><code style="font-size: 0.6rem;">{{ $log->ip_address ?? 'N/A' }}</code></td>
                            <td class="pe-2 py-1 text-end">
                                <a href="{{ route('verification-logs.show', $log->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="View Details">
                                    <i class="ti ti-eye"></i>
                                </a>
                                @if($log->is_archived)
                                    <form action="{{ route('verification-logs.unarchive', $log->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="Restore to Active">
                                            <i class="ti ti-archive-off"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('verification-logs.archive', $log->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="Archive">
                                            <i class="ti ti-archive"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                <i class="ti ti-scan" style="font-size: 48px;"></i>
                                <p class="mt-2">{{ ($isArchivedView ?? false) ? 'No archived scans' : 'No verification scans found' }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <x-pagination :items="$logs" :perPageOptions="[10, 25, 50, 100]" :ajax="true" />
        </div>
    </div>
