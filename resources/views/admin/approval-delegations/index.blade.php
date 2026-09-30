@extends('layouts.master')

@section('title', 'Approval Delegations')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Approval Delegations', 'url' => route('admin.approval-delegations.index')]
    ]" />

    <x-page-header title="APPROVAL DELEGATIONS" description="Temporarily delegate your approval authority to another staff member.">
        <a href="{{ route('admin.approval-delegations.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">New Delegation</a>
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2">
        <!-- Main Column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-replace text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Delegations</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" style="font-size: 0.75rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-2">Delegator</th>
                                    <th>Delegate</th>
                                    <th>Level</th>
                                    <th>Period</th>
                                    <th>Status</th>
                                    <th class="pe-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($delegations as $delegation)
                                <tr>
                                    <td class="ps-2">{{ optional($delegation->delegator)->full_name }}</td>
                                    <td>{{ optional($delegation->delegate)->full_name }}</td>
                                    <td>{{ strtoupper(str_replace('_', ' ', $delegation->level)) }}</td>
                                    <td>{{ $delegation->start_date?->format('d M Y') }} - {{ $delegation->end_date?->format('d M Y') }}</td>
                                    <td><span class="badge bg-{{ $delegation->status == 'active' ? 'success' : ($delegation->status == 'revoked' ? 'danger' : 'secondary') }}">{{ ucfirst($delegation->status) }}</span></td>
                                    <td class="pe-2">
                                        @if($delegation->status == 'active')
                                        <form method="POST" action="{{ route('admin.approval-delegations.destroy', $delegation->id) }}" class="d-inline" onsubmit="return confirm('Revoke this delegation?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">Revoke</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No approval delegations found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-1 px-2">
                    <x-pagination :items="$delegations" />
                </div>
            </div>
        </div>

        <!-- Info Column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-replace text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">What This Does</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Grants a delegate temporary authority to approve at a given level (HOD, Dean, DVC, HR, HR Logistics) on behalf of the real approver, for a date range - e.g. to cover someone who is away.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-lock text-danger mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Self-Approval Is Never Allowed</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">A delegate can never approve their own leave request through a delegation, even if it falls within an active delegation's level and date range.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-calendar-time text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Dated, Not Permanent</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Every delegation has a start and end date - it only grants authority within that window, and can be revoked early at any time.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-ban text-secondary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Revoking Is Immediate</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Revoked delegations stop applying right away and are kept in this list, marked Revoked, for the record.</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
