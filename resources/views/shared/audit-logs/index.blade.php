@extends('layouts.master')

@section('title', 'Audit Logs')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Administration', 'url' => '#'],
        ['name' => 'Audit Logs', 'url' => '#']
    ]" />
    
    <x-page-header title="AUDIT LOGS" description="System activity log">
    </x-page-header>
    
    <!-- Stats -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $logs->total() }}" label="Total Logs" subtext="system-wide" />
        <x-stats-card value="{{ $logs->where('action', 'create')->count() }}" label="Created" subtext="new records" color="success" />
        <x-stats-card value="{{ $logs->where('action', 'update')->count() }}" label="Updated" subtext="modified records" color="warning" />
        <x-stats-card value="{{ $logs->where('action', 'delete')->count() }}" label="Deleted" subtext="removed records" color="danger" />
    </div>
    
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-history text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Activity Logs</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() ?? 0 }}
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Time</th>
                            <th class="py-1">User</th>
                            <th class="py-1">Action</th>
                            <th class="py-1">Module</th>
                            <th class="py-1">Record ID</th>
                            <th class="py-1">IP Address</th>
                            <th class="pe-2 py-1">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-2 py-1">{{ \Carbon\Carbon::parse($log->created_at)->format('d M H:i:s') }}</td>
                            <td class="py-1">
                                {{ $log->user->first_name ?? 'System' }} {{ $log->user->last_name ?? '' }}
                                <div class="text-muted" style="font-size: 0.55rem;">{{ $log->user->staff_id ?? '' }}</div>
                            </td>
                            <td class="py-1">
                                @php
                                    $badgeClass = match($log->action) {
                                        'create' => 'success',
                                        'update' => 'warning',
                                        'delete' => 'danger',
                                        'approve' => 'info',
                                        'reject' => 'danger',
                                        'login' => 'primary',
                                        'logout' => 'secondary',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}-subtle text-{{ $badgeClass }} rounded-sm">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="py-1">{{ ucfirst($log->module) }}</td>
                            <td class="py-1">{{ $log->record_id ?? '-' }}</td>
                            <td class="py-1">{{ $log->ip_address ?? '-' }}</td>
                            <td class="pe-2 py-1">
                                @if($log->old_values || $log->new_values)
                                <button type="button" class="btn btn-outline-info btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $log->id }}" style="padding: 0.1rem 0.3rem;">
                                    <i class="ti ti-eye"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        
                        <!-- Details Modal -->
                        <div class="modal fade" id="detailsModal{{ $log->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-sm">
                                    <div class="modal-header py-1 px-2">
                                        <h5 class="modal-title" style="font-size: 0.8rem;">Change Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body py-2 px-2">
                                        @if($log->old_values)
                                        <div class="mb-2">
                                            <div class="text-muted" style="font-size: 0.65rem;">Before</div>
                                            <pre class="bg-light p-1 rounded-sm" style="font-size: 0.6rem;">{{ is_string($log->old_values) ? json_encode(json_decode($log->old_values), JSON_PRETTY_PRINT) : json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                        @endif
                                        @if($log->new_values)
                                        <div>
                                            <div class="text-muted" style="font-size: 0.65rem;">After</div>
                                            <pre class="bg-light p-1 rounded-sm" style="font-size: 0.6rem;">{{ is_string($log->new_values) ? json_encode(json_decode($log->new_values), JSON_PRETTY_PRINT) : json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted">No audit logs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection