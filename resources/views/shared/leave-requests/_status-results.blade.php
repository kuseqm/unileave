    <div class="card-body p-0">
        @forelse($leaveRequests as $request)
        <div class="request-item p-3 border-bottom">
            <!-- Request Header -->
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-semibold" style="font-size: 0.85rem;">
                            {{ $request->leaveType->name ?? 'Leave Request' }}
                        </span>
                        <span class="badge {{
                            $request->status == 'pending' ? 'bg-warning-subtle text-warning' :
                            ($request->status == 'approved' ? 'bg-success-subtle text-success' :
                            ($request->status == 'rejected' ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary'))
                        }} rounded-sm px-2 py-1" style="font-size: 0.6rem;">
                            {{ ucfirst($request->status) }}
                        </span>
                        @if($request->current_approval_level)
                        <span class="badge bg-info-subtle text-info rounded-sm px-2 py-1" style="font-size: 0.6rem;">
                            @php
                                $levelNames = [1 => 'HOD', 2 => 'Dean', 3 => 'HR', 4 => 'DVC', 5 => 'HR Logistics'];
                            @endphp
                            {{ $levelNames[$request->current_approval_level] ?? 'Processing' }}
                        </span>
                        @endif
                    </div>
                    <div class="text-muted" style="font-size: 0.65rem; margin-top: 4px;">
                        <i class="ti ti-calendar me-1"></i>
                        {{ \Carbon\Carbon::parse($request->start_date)->format('M d, Y') }} -
                        {{ \Carbon\Carbon::parse($request->end_date)->format('M d, Y') }}
                        <span class="mx-1">•</span>
                        <i class="ti ti-clock me-1"></i>{{ $request->days }} days
                    </div>
                </div>
                <div>
                    <a href="{{ route('leave-requests.show', $request->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-eye me-1"></i> Details
                    </a>
                    @if($request->status == 'pending' && !$request->current_approval_level)
                    <button onclick="cancelRequest({{ $request->id }})" class="btn btn-outline-danger btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-x me-1"></i> Cancel
                    </button>
                    @endif
                </div>
            </div>

            <!-- Approval Tracker (using FULL tracker with details) -->
            <div class="mt-2">
                <x-approval-tracker-full :leaveRequest="$request" />
            </div>

            <!-- Brief Timeline Summary -->
            @if($request->approvalLogs && $request->approvalLogs->isNotEmpty())
            <div class="mt-2 pt-2 border-top" style="border-color: #E8E2D5;">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <span class="text-muted" style="font-size: 0.55rem;">Latest activity:</span>
                    @foreach($request->approvalLogs->take(2) as $log)
                    <div class="d-flex align-items-center gap-1">
                        <i class="ti ti-{{ $log->action == 'approved' ? 'check-circle' : ($log->action == 'rejected' ? 'x-circle' : 'clock') }}"
                           style="font-size: 0.6rem; color: {{ $log->action == 'approved' ? '#10B981' : ($log->action == 'rejected' ? '#EF4444' : '#F59E0B') }};"></i>
                        <span style="font-size: 0.6rem;">
                            {{ ucfirst($log->action) }} @ {{ $log->created_at->format('M d, H:i') }}
                            @if($log->approver)
                            by {{ $log->approver->first_name ?? '' }}
                            @endif
                        </span>
                    </div>
                    @endforeach
                    @if($request->approvalLogs->count() > 2)
                    <span class="text-muted" style="font-size: 0.55rem;">+{{ $request->approvalLogs->count() - 2 }} more</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-4">
            <i class="ti ti-calendar-off" style="font-size: 2rem; color: #CBD5E1;"></i>
            <p class="text-muted mt-2" style="font-size: 0.75rem;">You haven't submitted any leave requests yet.</p>
            <a href="{{ route('leave-requests.create') }}" class="btn btn-primary btn-sm rounded-sm mt-2" style="font-size: 0.7rem;">
                <i class="ti ti-plus me-1"></i> Submit Your First Request
            </a>
        </div>
        @endforelse
    </div>
    <div class="card-footer py-1 px-2">
        <x-pagination :items="$leaveRequests" :perPageOptions="[10, 25, 50, 100]" :ajax="true" />
    </div>
