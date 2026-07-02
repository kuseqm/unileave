@switch($status)
    @case('draft')
        <span class="badge bg-secondary rounded-sm">Draft</span>
        @break
    @case('submitted')
        <span class="badge bg-info rounded-sm">Submitted</span>
        @break
    @case('pending_approval')
        <span class="badge bg-warning rounded-sm">Pending Approval</span>
        @break
    @case('approved')
        <span class="badge bg-primary rounded-sm">Approved</span>
        @break
    @case('signed')
        <span class="badge bg-success rounded-sm">Signed</span>
        @break
    @case('printed')
        <span class="badge bg-dark rounded-sm">Printed</span>
        @break
    @case('archived')
        <span class="badge bg-secondary rounded-sm">Archived</span>
        @break
    @default
        <span class="badge bg-light text-dark rounded-sm">{{ ucfirst($status) }}</span>
@endswitch