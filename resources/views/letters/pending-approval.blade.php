@extends('layouts.master')

@section('title', 'Pending Approvals')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Letters', 'url' => route('letters.index')],
        ['name' => 'Pending Approvals', 'url' => '#']
    ]" />
    
    <x-page-header title="PENDING APPROVALS" description="Letters awaiting your approval">
    </x-page-header>
    
    <x-alert-success />
    
    <x-alert-danger />
    
    @php
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        $currentLevel = '';
        
        if ($role === 'hod') {
            $currentLevel = 'HOD';
            $levelDescription = 'Head of Department - Letters from your department';
        } elseif ($role === 'dean') {
            $currentLevel = 'Dean';
            $levelDescription = 'Dean of Faculty - Letters from your faculty';
        } elseif (in_array($role, ['dvca', 'dvc-pfa'])) {
            $currentLevel = 'DVC';
            $levelDescription = $role === 'dvca' ? 'DVC Academic - Academic staff letters' : 'DVC PFA - Administrative/Support staff letters';
        } else {
            $currentLevel = 'Approver';
            $levelDescription = 'Letters pending your approval';
        }
    @endphp
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Pending Letters" subtext="awaiting your {{ $currentLevel }} approval" color="warning" />
        <x-stats-card value="{{ $stats['this_week'] }}" label="This Week" subtext="submitted in last 7 days" color="info" />
        <x-stats-card value="{{ $stats['today'] }}" label="Today" subtext="submitted in last 24 hours" color="danger" />
        <x-stats-card value="{{ $stats['overdue'] }}" label="Overdue" subtext="waiting over 7 days" color="dark" />
    </div>
    
    <!-- Info Alert -->
    <x-alert-info>
    <strong>Your Role:</strong> {{ ucfirst($role) }} | 
        <strong>{{ $levelDescription }}</strong> |
        <strong>Approval Action:</strong> Click "Approve" to approve this letter. Your name, timestamp, and IP address will be recorded for audit purposes.
</x-alert-info>
    
    <div class="row g-2">
        <!-- Pending Approvals Table - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <i class="ti ti-clock text-warning me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Letters Awaiting Your Approval</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    {{ $letters->total() }} letter(s) pending
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($letters->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">#</th>
                            <th class="py-1">Letter Number</th>
                            <th class="py-1">Title</th>
                            <th class="py-1">From (Sender)</th>
                            <th class="py-1">To (Recipient)</th>
                            <th class="py-1">Submitted</th>
                            <th class="py-1">Approval Status</th>
                            <th class="pe-2 py-1" width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($letters as $index => $letter)
                        @php
                            // Determine current approval stage
                            $approvalStage = 'hod';
                            if ($letter->hod_approved_at) $approvalStage = 'dean';
                            if ($letter->dean_approved_at) $approvalStage = 'dvc';
                            if ($letter->dvc_approved_at) $approvalStage = 'completed';
                            
                            $stageLabel = '';
                            $stageClass = '';
                            if ($approvalStage === 'hod') {
                                $stageLabel = 'Pending HOD';
                                $stageClass = 'warning';
                            } elseif ($approvalStage === 'dean') {
                                $stageLabel = 'Pending Dean';
                                $stageClass = 'info';
                            } elseif ($approvalStage === 'dvc') {
                                $stageLabel = 'Pending DVC';
                                $stageClass = 'primary';
                            }
                        @endphp
                        <tr>
                            <td class="ps-2 py-1">{{ $letters->firstItem() + $index }}</td>
                            <td class="py-1">{{ $letter->letter_number ?? 'N/A' }}</td>
                            <td class="py-1">{{ Str::limit($letter->title, 40) }}</td>
                            <td class="py-1">{{ $letter->sender->full_name ?? 'N/A' }}</td>
                            <td class="py-1">{{ $letter->recipient->full_name ?? 'N/A' }}</td>
                            <td class="py-1">{{ $letter->submitted_at ? $letter->submitted_at->format('d M Y') : 'N/A' }}</td>
                            <td class="py-1">
                                <span class="badge bg-{{ $stageClass }}-subtle text-{{ $stageClass }} rounded-sm" style="font-size: 0.6rem;">
                                    {{ $stageLabel }}
                                </span>
                            </td>
                            <td class="pe-2 py-1">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('letters.show', $letter->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="View Letter">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    
                                    @if($role === 'hod' && !$letter->hod_approved_at)
                                        <form action="{{ route('letters.hod.approve', $letter->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="button" class="btn btn-sm btn-success rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="Approve as HOD" onclick="confirmApproval(this, 'HOD')">
                                                <i class="ti ti-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($role === 'dean' && $letter->hod_approved_at && !$letter->dean_approved_at)
                                        <form action="{{ route('letters.dean.approve', $letter->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="button" class="btn btn-sm btn-success rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="Approve as Dean" onclick="confirmApproval(this, 'Dean')">
                                                <i class="ti ti-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($role, ['dvca', 'dvc-pfa']) && $letter->hod_approved_at && $letter->dean_approved_at && !$letter->dvc_approved_at)
                                        <form action="{{ route('letters.dvc.approve', $letter->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="button" class="btn btn-sm btn-success rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="Approve as DVC" onclick="confirmApproval(this, 'DVC')">
                                                <i class="ti ti-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4 text-muted" style="font-size: 0.7rem;">
                <i class="ti ti-check-circle fs-4"></i>
                <p class="mb-0 mt-2">No pending approvals at this time.</p>
                <small>All caught up! Check back later for new submissions.</small>
            </div>
            @endif
        </div>
        
        @if($letters->hasPages())
        <div class="card-footer py-2 px-2 bg-light">
            {{ $letters->links() }}
        </div>
        @endif
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> Approval Chain</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>HOD approves first</li>
                            <li>Dean approves next</li>
                            <li>DVC approves last, before it moves to signature</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Only your role's approval action shows on each row</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Approvals are logged with name, time, and IP for audit</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmApproval(btn, level) {
        confirmAction(
            'Are you sure you want to approve this letter as ' + level + '?\n\nYour name, timestamp, and IP address will be recorded for audit purposes.\n\nThis action cannot be undone.',
            function () { btn.closest('form').submit(); },
            { danger: false, confirmText: 'Approve' }
        );
    }
</script>
@endsection