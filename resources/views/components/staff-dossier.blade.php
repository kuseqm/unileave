@props(['staff', 'leaveRequest' => null, 'compact' => false])

@php
    // Get staff leave history (last 12 months)
    $leaveHistory = App\Models\LeaveRequest::where('staff_id', $staff->id)
        ->where('created_at', '>=', now()->subMonths(12))
        ->with('leaveType')
        ->orderBy('created_at', 'desc')
        ->get();
    
    // Calculate compliance score (on-time returns)
    $totalLeaves = $leaveHistory->where('status', 'approved')->count();
    $onTimeReturns = 0; // This would need actual return tracking
    $complianceScore = $totalLeaves > 0 ? round(($onTimeReturns / $totalLeaves) * 100) : 100;
    
    // Get leave balances
    $leaveBalances = App\Models\LeaveBalance::where('staff_id', $staff->id)->get();
    
    // Get acting history (staff acted for others)
    $actedForOthers = App\Models\HoldPositionHistory::where('holder_staff_id', $staff->id)
        ->with('leaveRequest')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    // Get hold history (others acted for staff)
    $othersActedFor = App\Models\HoldPositionHistory::where('requester_staff_id', $staff->id)
        ->with('holder')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    // Calculate trust score based on acting history
    $acceptedCount = $actedForOthers->where('status', 'accepted')->count();
    $declinedCount = $actedForOthers->where('status', 'declined')->count();
    $trustScore = ($acceptedCount + $declinedCount) > 0 
        ? round(($acceptedCount / ($acceptedCount + $declinedCount)) * 100) 
        : 0;
@endphp

@if($compact)
    <!-- Compact View (for table rows) -->
    <div class="staff-dossier-compact">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <span class="fw-semibold" style="font-size: 0.7rem;">{{ strtoupper(substr($staff->first_name, 0, 1)) }}{{ strtoupper(substr($staff->last_name, 0, 1)) }}</span>
            </div>
            <div>
                <div class="fw-semibold" style="font-size: 0.75rem;">{{ $staff->full_name }}</div>
                <div class="text-muted" style="font-size: 0.6rem;">{{ $staff->staff_id }} | {{ $staff->department->name ?? 'N/A' }}</div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="d-flex gap-3 mt-2">
            <div>
                <span class="text-muted" style="font-size: 0.55rem;">Leave Balance</span>
                <div class="fw-semibold" style="font-size: 0.7rem;">
                    @php
                        $totalBalance = $leaveBalances->sum('balance');
                    @endphp
                    {{ number_format($totalBalance, 1) }} days
                </div>
            </div>
            <div>
                <span class="text-muted" style="font-size: 0.55rem;">Trust Score</span>
                <div class="fw-semibold" style="font-size: 0.7rem; color: {{ $trustScore >= 80 ? '#10B981' : ($trustScore >= 50 ? '#F59E0B' : '#EF4444') }}">
                    {{ $trustScore }}%
                </div>
            </div>
            <div>
                <span class="text-muted" style="font-size: 0.55rem;">Compliance</span>
                <div class="fw-semibold" style="font-size: 0.7rem; color: {{ $complianceScore >= 80 ? '#10B981' : '#F59E0B' }}">
                    {{ $complianceScore }}%
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Full View (for modal/dedicated page) -->
    <div class="staff-dossier">
        <!-- Header with Staff Info -->
        <div class="d-flex align-items-center gap-3 mb-3 pb-2 border-bottom">
            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <span class="fw-semibold" style="font-size: 1.2rem;">{{ strtoupper(substr($staff->first_name, 0, 1)) }}{{ strtoupper(substr($staff->last_name, 0, 1)) }}</span>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap">
                    <div>
                        <h4 class="mb-0">{{ $staff->full_name }}</h4>
                        <div class="text-muted" style="font-size: 0.7rem;">{{ $staff->staff_id }} | {{ $staff->job_title ?? 'Staff Member' }}</div>
                        <div class="text-muted" style="font-size: 0.65rem;">{{ $staff->department->name ?? 'N/A' }} | Joined: {{ optional($staff->joining_date)->format('M Y') ?? 'N/A' }}</div>
                    </div>
                    <div class="text-end">
                        <div class="badge bg-{{ $staff->status == 'active' ? 'success' : 'danger' }}-subtle text-{{ $staff->status == 'active' ? 'success' : 'danger' }} rounded-sm px-2 py-1">
                            {{ ucfirst($staff->status) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Row -->
        <div class="row g-2 mb-3">
            <div class="col-md-3">
                <div class="p-2 bg-light rounded-sm text-center">
                    <div class="text-muted" style="font-size: 0.6rem;">Total Leave Taken (12m)</div>
                    <div class="fw-semibold" style="font-size: 1rem;">{{ $leaveHistory->where('status', 'approved')->sum('days') }} days</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-2 bg-light rounded-sm text-center">
                    <div class="text-muted" style="font-size: 0.6rem;">Current Balance</div>
                    <div class="fw-semibold" style="font-size: 1rem;">{{ number_format($leaveBalances->sum('balance'), 1) }} days</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-2 bg-light rounded-sm text-center">
                    <div class="text-muted" style="font-size: 0.6rem;">Trust Score</div>
                    <div class="fw-semibold" style="font-size: 1rem; color: {{ $trustScore >= 80 ? '#10B981' : ($trustScore >= 50 ? '#F59E0B' : '#EF4444') }}">
                        {{ $trustScore }}%
                    </div>
                    <div class="text-muted" style="font-size: 0.55rem;">Based on {{ $acceptedCount + $declinedCount }} acting requests</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-2 bg-light rounded-sm text-center">
                    <div class="text-muted" style="font-size: 0.6rem;">Compliance Rate</div>
                    <div class="fw-semibold" style="font-size: 1rem; color: {{ $complianceScore >= 80 ? '#10B981' : '#F59E0B' }}">
                        {{ $complianceScore }}%
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Leave History Table -->
        <div class="mb-3">
            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">Recent Leave History (12 months)</div>
            <div class="table-responsive">
                <table class="table table-sm" style="font-size: 0.65rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-1">Dates</th>
                            <th>Type</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Acting Staff</th>
                        </thead>
                    <tbody>
                        @forelse($leaveHistory->take(5) as $history)
                        <tr>
                            <td class="ps-1">{{ \Carbon\Carbon::parse($history->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($history->end_date)->format('d M Y') }}</td>
                            <td>{{ $history->leaveType->name ?? 'N/A' }}</td>
                            <td>{{ $history->days }}</td>
                            <td>
                                @if($history->status == 'approved')
                                    <span class="badge bg-success-subtle text-success rounded-sm">Approved</span>
                                @elseif($history->status == 'rejected')
                                    <span class="badge bg-danger-subtle text-danger rounded-sm">Rejected</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-sm">Pending</span>
                                @endif
                            </td>
                            <td>{{ $history->actingStaff->full_name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">No leave history</td><?
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Acting History -->
        <div class="row g-2">
            <div class="col-md-6">
                <div class="fw-semibold mb-1" style="font-size: 0.7rem;">Acted For Others</div>
                @forelse($actedForOthers as $act)
                <div class="p-1 mb-1 bg-light rounded-sm" style="font-size: 0.65rem;">
                    <div class="d-flex justify-content-between">
                        <span>{{ $act->leaveRequest->staff->full_name ?? 'N/A' }}</span>
                        <span class="badge bg-{{ $act->status == 'accepted' ? 'success' : 'danger' }}-subtle rounded-sm">{{ ucfirst($act->status) }}</span>
                    </div>
                    <div class="text-muted" style="font-size: 0.55rem;">
                        {{ \Carbon\Carbon::parse($act->requested_at)->format('d M Y') }}
                    </div>
                </div>
                @empty
                <div class="text-muted text-center py-1" style="font-size: 0.6rem;">No acting history</div>
                @endforelse
            </div>
            <div class="col-md-6">
                <div class="fw-semibold mb-1" style="font-size: 0.7rem;">Others Acted For Staff</div>
                @forelse($othersActedFor as $act)
                <div class="p-1 mb-1 bg-light rounded-sm" style="font-size: 0.65rem;">
                    <div class="d-flex justify-content-between">
                        <span>{{ $act->holder->full_name ?? 'N/A' }}</span>
                        <span class="badge bg-{{ $act->status == 'accepted' ? 'success' : 'danger' }}-subtle rounded-sm">{{ ucfirst($act->status) }}</span>
                    </div>
                    <div class="text-muted" style="font-size: 0.55rem;">
                        {{ \Carbon\Carbon::parse($act->requested_at)->format('d M Y') }}
                    </div>
                </div>
                @empty
                <div class="text-muted text-center py-1" style="font-size: 0.6rem;">No hold history</div>
                @endforelse
            </div>
        </div>
    </div>
@endif