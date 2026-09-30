@php
    $panelId = $id ?? 'panel';
    $subtabs = ['all' => 'list-details', 'leave' => 'calendar', 'permission' => 'clock', 'travel' => 'plane'];
    $subtabLabels = ['all' => 'All', 'leave' => 'Leave', 'permission' => 'Permission', 'travel' => 'Travel'];
    $stats = $stats ?? [];
    $counts = $counts ?? [];
@endphp

<div class="row g-2 mb-3">
    <x-stats-card value="{{ $stats['total'] ?? 0 }}" label="Total" subtext="all modules" color="primary" icon="list-details" />
    <x-stats-card value="{{ $stats['leave'] ?? 0 }}" label="Leave" subtext="requests" color="info" icon="calendar" />
    <x-stats-card value="{{ $stats['permission'] ?? 0 }}" label="Permission" subtext="requests" color="warning" icon="clock" />
    <x-stats-card value="{{ $stats['travel'] ?? 0 }}" label="Travel" subtext="requests" color="secondary" icon="plane" />
    <x-stats-card value="{{ $stats['overdue'] ?? 0 }}" label="Overdue" subtext="2+ days" color="danger" icon="alert-triangle" />
    <x-stats-card value="{{ $stats['acting'] ?? 0 }}" label="Acting Stage" subtext="awaiting acting staff" color="info" icon="user-check" />
    <x-stats-card value="{{ $stats['departmental'] ?? 0 }}" label="Departmental" subtext="HOD, Dean, DVC, Unit Head" color="primary" icon="building" />
    <x-stats-card value="{{ $stats['unassigned'] ?? 0 }}" label="Unassigned" subtext="nobody to act" color="danger" icon="user-off" />
</div>

<div class="card rounded-sm">
    <div class="card-header py-1 px-2 bg-light">
        <ul class="nav nav-tabs card-header-tabs" id="{{ $panelId }}Tabs" role="tablist" style="font-size: 0.8rem;">
            @foreach($subtabs as $key => $icon)
                @php
                    $isActive = ($activeSubtab ?? 'all') === $key;
                    $url = request()->fullUrlWithQuery(array_merge(request()->except('page'), ['subtab' => $key]));
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ $url }}" role="tab" style="font-size: 0.7rem;">
                        <i class="ti ti-{{ $icon }} me-1"></i> {{ $subtabLabels[$key] }}
                        <span class="badge bg-primary ms-1 rounded-pill" style="font-size: 0.65rem;">{{ $counts[$key] ?? 0 }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="card-body p-0">
        @include('shared.applications-overview._table', ['rows' => $paginator, 'activeTab' => $activeTab ?? 'in-progress'])

        @if($paginator->total() > 0)
        <div class="p-2 border-top">
            <x-pagination :items="$paginator" id="{{ $panelId }}" :perPageOptions="[10, 25, 50]" />
        </div>
        @endif
    </div>
</div>
