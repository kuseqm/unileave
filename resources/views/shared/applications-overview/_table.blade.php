@php
    $moduleColors = ['leave' => 'primary', 'permission' => 'warning', 'travel' => 'secondary'];
    $moduleIcons = ['leave' => 'calendar', 'permission' => 'clock', 'travel' => 'plane'];
@endphp

@if($rows->count() > 0)
<div class="table-responsive">
    <table class="table table-sm mb-0" style="font-size: 0.75rem;">
        <thead class="bg-light">
            <tr>
                <th class="ps-2 py-1">Module</th>
                <th class="py-1">Applicant</th>
                <th class="py-1">Type</th>
                <th class="py-1">Stage</th>
                @if(($activeTab ?? 'in-progress') !== 'completed')
                <th class="py-1">Waiting On</th>
                <th class="py-1">Pending</th>
                @else
                <th class="py-1">Reporting Date</th>
                @endif
                <th class="pe-2 py-1 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $item)
            <tr>
                <td class="ps-2 py-1">
                    <span class="badge bg-{{ $moduleColors[$item['module']] }}-subtle text-{{ $moduleColors[$item['module']] }} rounded-sm">
                        <i class="ti ti-{{ $moduleIcons[$item['module']] }} me-1"></i>{{ ucfirst($item['module']) }}
                    </span>
                </td>
                <td class="py-1 fw-semibold">{{ $item['applicant'] }}</td>
                <td class="py-1">{{ $item['type_label'] }}</td>
                <td class="py-1">{{ $item['stage_label'] }}</td>
                @if(($activeTab ?? 'in-progress') !== 'completed')
                <td class="py-1">
                    @if($item['waiting_on_label'])
                        {{ $item['waiting_on_label'] }}
                        @if($item['waiting_on_count'] > 1)
                            <span class="text-muted">({{ $item['waiting_on_count'] }} people)</span>
                        @elseif($item['waiting_on_count'] === 0)
                            <span class="badge bg-danger-subtle text-danger rounded-sm">unassigned</span>
                        @endif
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="py-1">
                    <span class="{{ $item['days_pending'] >= 2 ? 'text-danger fw-semibold' : 'text-muted' }}">
                        {{ $item['days_pending'] }} day(s)
                    </span>
                </td>
                @else
                <td class="py-1">{{ $item['reporting_date'] ?? 'N/A' }}</td>
                @endif
                <td class="pe-2 py-1 text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        <a href="{{ $item['show_route'] }}" class="btn btn-sm btn-outline-info rounded-sm" title="View" style="padding: 0.15rem 0.3rem;">
                            <i class="ti ti-eye" style="font-size: 0.7rem;"></i>
                        </a>
                        @if($item['waiting_on_count'] > 0)
                        <form action="{{ route('applications-overview.remind', [$item['module'], $item['id']]) }}" method="POST">
                            @csrf
                            <button type="button" class="btn btn-sm btn-outline-warning rounded-sm" title="Send Reminder" style="padding: 0.15rem 0.3rem;" onclick="confirmSubmit(this, 'Send a reminder to {{ $item['waiting_on_label'] }}?', { danger: false, confirmText: 'Send Reminder' })">
                                <i class="ti ti-bell-ringing" style="font-size: 0.7rem;"></i>
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
<div class="text-center py-4 text-muted">
    <i class="ti ti-circle-check mb-2 d-block" style="font-size: 1.5rem;"></i>
    <p class="mb-0" style="font-size: 0.75rem;">Nothing here right now.</p>
</div>
@endif
