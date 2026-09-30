@props([
    'placeholder' => 'Search...',
    'id' => 'searchInput',
    'name' => null,
    'value' => '',
    'showStatusFilter' => false,
    'showCategoryFilter' => false,
    'showTypeFilter' => false,
    'showDateRange' => false,
    'statusOptions' => [],
    'categoryOptions' => [],
    'typeOptions' => [],
    'statusLabel' => 'Status',
    'categoryLabel' => 'Category',
    'typeLabel' => 'Type',
    'dateFromValue' => '',
    'dateToValue' => '',
    'onReset' => null,
    'title' => 'Search',
    // Filter-form mode (no live search): set `action` to make the card a GET form
    // with Apply/Reset, turn the text box off with :showSearch="false" and put the
    // fields in the named slot `filters` (e.g. x-scrollable-dropdown columns).
    'action' => null,
    'resetUrl' => null,
    'showSearch' => true,
    'icon' => 'ti-search',
    'autoSubmit' => false,
])

<div class="card rounded-sm mb-2">
    <div class="card-header py-1 px-2 bg-light">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="ti {{ $icon }} text-muted me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">{{ $title }}</span>
            </div>
            @isset($slot)
                @if(trim($slot))
                <div class="text-muted" style="font-size: 0.65rem;">{{ $slot }}</div>
                @endif
            @endisset
        </div>
    </div>
    <div class="card-body py-2 px-2">
        @if($action)<form method="GET" action="{{ $action }}">@endif
        <div class="row g-1 align-items-end">
            @if($showSearch)
            <div class="col-md-4 position-relative">
                <label class="form-label" style="font-size: 0.65rem;">Search</label>
                <input type="text" id="{{ $id }}" @if($name) name="{{ $name }}" @endif value="{{ $value }}" autocomplete="off" class="form-control form-control-sm rounded-sm" placeholder="{{ $placeholder }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                @if($name)
                <div id="{{ $id }}Suggestions" class="search-suggestions d-none"></div>
                @endif
            </div>
            @endif

            {{ $filters ?? '' }}
            
            @if($showStatusFilter)
            <div class="col-md-3">
                <label class="form-label" style="font-size: 0.65rem;">{{ $statusLabel }}</label>
                <select id="statusFilter" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                    <option value="">All</option>
                    @foreach($statusOptions as $key => $label)
                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            
            @if($showCategoryFilter)
            <div class="col-md-3">
                <label class="form-label" style="font-size: 0.65rem;">{{ $categoryLabel }}</label>
                <select id="categoryFilter" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                    <option value="">All</option>
                    @foreach($categoryOptions as $key => $label)
                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($showTypeFilter)
            <div class="col-md-3">
                <label class="form-label" style="font-size: 0.65rem;">{{ $typeLabel }}</label>
                <select id="typeFilter" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                    <option value="">All</option>
                    @foreach($typeOptions as $key => $label)
                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($showDateRange)
            <div class="col-md-2">
                <label class="form-label" style="font-size: 0.65rem;">From</label>
                <input type="date" id="dateFromFilter" value="{{ $dateFromValue }}" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size: 0.65rem;">To</label>
                <input type="date" id="dateToFilter" value="{{ $dateToValue }}" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
            </div>
            @endif

            @if($action)
            <div class="col-auto d-flex gap-1">
                @if(! $autoSubmit)
                <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;"><i class="ti ti-filter me-1"></i> Apply</button>
                @endif
                <a href="{{ $resetUrl ?? $action }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;"><i class="ti ti-refresh me-1"></i> Reset</a>
            </div>
            @else
            <div class="col-md-2">
                <button id="resetFilters" class="btn btn-outline-secondary btn-sm w-100 rounded-sm" style="font-size: 0.65rem; padding: 0.25rem 0.3rem;">
                    <i class="ti ti-refresh me-1"></i> Reset
                </button>
            </div>
            @endif
        </div>
        @if($action)</form>@endif
    </div>
</div>

<style>
.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0.75rem;
    right: 0.75rem;
    z-index: 20;
    background: white;
    border: 1px solid #E8E2D5;
    border-radius: 0.25rem;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    /* Caps it to ~3 visible rows and scrolls beyond that, instead of the
     * browser's native autocomplete list (which we can't size or limit and
     * can otherwise sprawl down the whole page). */
    max-height: 96px;
    overflow-y: auto;
}
.search-suggestions .search-suggestion-item {
    padding: 0.3rem 0.5rem;
    font-size: 0.7rem;
    cursor: pointer;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.search-suggestions .search-suggestion-item:hover {
    background: #F1F5F9;
    color: #1E293B;
}
.search-suggestions .search-suggestion-item + .search-suggestion-item {
    border-top: 1px solid #F1F5F9;
}
</style>