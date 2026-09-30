    <div class="card-body p-0">
        @forelse($holdHistories as $history)
        <div class="request-item p-3 border-bottom">
            <!-- Request Header -->
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-semibold" style="font-size: 0.85rem;">
                            @if($history->holder)
                            <a href="{{ route('leave-requests.holder', $history->id) }}" class="text-decoration-none">
                                {{ $history->holder->first_name }} {{ $history->holder->last_name }}
                            </a>
                            @else
                            Unknown
                            @endif
                        </span>
                        <span class="badge {{
                            $history->status == 'pending' ? 'bg-warning-subtle text-warning' :
                            ($history->status == 'accepted' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger')
                        }} rounded-sm px-2 py-1" style="font-size: 0.6rem;">
                            {{ $history->status == 'pending' ? 'Awaiting Their Response' : ucfirst($history->status) }}
                        </span>
                    </div>
                    @if($history->holder)
                    <div class="text-muted" style="font-size: 0.6rem; margin-top: 2px;">
                        <i class="ti ti-id me-1"></i>{{ $history->holder->staff_id ?? 'Not provided' }}
                        <span class="mx-1">•</span>
                        <i class="ti ti-building me-1"></i>{{ $history->holder->department->name ?? 'Not assigned' }}
                    </div>
                    @endif
                    <div class="text-muted" style="font-size: 0.65rem; margin-top: 4px;">
                        <i class="ti ti-calendar me-1"></i>
                        @if($history->leaveRequest)
                        {{ \Carbon\Carbon::parse($history->leaveRequest->start_date)->format('M d, Y') }} -
                        {{ \Carbon\Carbon::parse($history->leaveRequest->end_date)->format('M d, Y') }}
                        <span class="mx-1">•</span>
                        <i class="ti ti-clock me-1"></i>{{ $history->leaveRequest->days }} days
                        @else
                        Not available
                        @endif
                    </div>
                </div>
                <div>
                    <a href="{{ route('leave-requests.holder', $history->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-eye me-1"></i> Details
                    </a>
                </div>
            </div>

            <!-- Leave Type and Reason -->
            <div class="mt-2">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="text-muted" style="font-size: 0.6rem;">Leave Type</div>
                        <div style="font-size: 0.7rem;">{{ optional($history->leaveRequest?->leaveType)->name ?? 'Not specified' }}</div>
                    </div>
                    <div class="col-md-8">
                        <div class="text-muted" style="font-size: 0.6rem;">Reason</div>
                        <div style="font-size: 0.7rem;">{{ $history->leaveRequest ? Str::limit($history->leaveRequest->reason, 100) : 'Not provided' }}</div>
                    </div>
                </div>
            </div>

            <!-- Their Response Comment if any -->
            @if($history->comment)
            <div class="mt-2 pt-2 border-top" style="border-color: #E8E2D5;">
                <div class="text-muted" style="font-size: 0.6rem;">{{ $history->holder->first_name ?? 'Their' }}'s Comment</div>
                <div class="small" style="font-size: 0.65rem;">"{{ $history->comment }}"</div>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-4">
            <i class="ti ti-user-check" style="font-size: 2rem; color: #CBD5E1;"></i>
            <p class="text-muted mt-2" style="font-size: 0.75rem;">No acting requests found.</p>
            <p class="text-muted" style="font-size: 0.65rem;">When you request someone to hold your position, it will appear here.</p>
        </div>
        @endforelse
    </div>
    <div class="card-footer py-1 px-2">
        <x-pagination :items="$holdHistories" :perPageOptions="[10, 25, 50, 100]" :ajax="true" />
    </div>
