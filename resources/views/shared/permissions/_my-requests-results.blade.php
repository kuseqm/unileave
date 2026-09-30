    <div class="card-body p-0">
        @forelse($permissionRequests as $request)
        <div class="request-item p-3 border-bottom">
            <!-- Request Header -->
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-semibold" style="font-size: 0.85rem;">
                            {{ str_replace('_', ' ', ucfirst($request->permission_type)) }}
                        </span>
                        <span class="badge {{
                            $request->status == 'pending' ? 'bg-warning-subtle text-warning' :
                            ($request->status == 'approved' ? 'bg-success-subtle text-success' :
                            ($request->status == 'cancelled' ? 'bg-secondary-subtle text-secondary' : 'bg-danger-subtle text-danger'))
                        }} rounded-sm px-2 py-1" style="font-size: 0.6rem;">
                            {{ ucfirst($request->status) }}
                        </span>
                    </div>
                    <div class="text-muted" style="font-size: 0.65rem; margin-top: 4px;">
                        <i class="ti ti-calendar me-1"></i>
                        {{ \Carbon\Carbon::parse($request->start_datetime)->format('M d, Y H:i') }} -
                        {{ \Carbon\Carbon::parse($request->end_datetime)->format('M d, Y H:i') }}
                        <span class="mx-1">•</span>
                        <i class="ti ti-clock me-1"></i>{{ $request->hours }} hour(s)
                    </div>
                </div>
                <div>
                    <a href="{{ route('permissions.show', $request->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-eye me-1"></i> Details
                    </a>
                </div>
            </div>

            <!-- Reason -->
            <div class="mt-2">
                <div class="text-muted" style="font-size: 0.6rem;">Reason</div>
                <div style="font-size: 0.7rem;">{{ Str::limit($request->reason, 150) }}</div>
            </div>

            <!-- Official Duty Description (if applicable) -->
            @if($request->permission_type == 'official_duty' && $request->official_duty_description)
            <div class="mt-2 pt-1">
                <div class="text-muted" style="font-size: 0.6rem;">Official Duty Description</div>
                <div style="font-size: 0.7rem;">{{ Str::limit($request->official_duty_description, 100) }}</div>
            </div>
            @endif

            <!-- Rejection Reason (if rejected) -->
            @if($request->status == 'rejected' && $request->rejection_reason)
            <div class="mt-2 pt-1">
                <div class="text-muted text-danger" style="font-size: 0.6rem;">Rejection Reason</div>
                <div class="text-danger" style="font-size: 0.7rem;">{{ $request->rejection_reason }}</div>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-4">
            <i class="ti ti-clock" style="font-size: 2rem; color: #CBD5E1;"></i>
            <p class="text-muted mt-2" style="font-size: 0.75rem;">You haven't submitted any permission requests yet.</p>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm rounded-sm mt-2" style="font-size: 0.7rem;">
                <i class="ti ti-plus me-1"></i> Request Permission
            </a>
        </div>
        @endforelse
    </div>
    <div class="card-footer py-1 px-2">
        <x-pagination :items="$permissionRequests" :perPageOptions="[10, 25, 50, 100]" :ajax="true" />
    </div>
