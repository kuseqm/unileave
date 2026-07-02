@props([
    'placeholder' => 'Search...',
    'id' => 'searchInput',
    'showStatusFilter' => false,
    'showCategoryFilter' => false,
    'showTypeFilter' => false,
    'statusOptions' => [],
    'categoryOptions' => [],
    'typeOptions' => [],
    'onReset' => null
])

<div class="card rounded-sm mb-2">
    <div class="card-header py-1 px-2 bg-light">
        <div class="d-flex align-items-center">
            <i class="ti ti-search text-muted me-1" style="font-size: 0.8rem;"></i>
            <span style="font-size: 0.7rem; font-weight: 600;">Sophisticated Search</span>
        </div>
    </div>
    <div class="card-body py-2 px-2">
        <div class="row g-1 align-items-end">
            <div class="col-md-4">
                <label class="form-label" style="font-size: 0.65rem;">Search</label>
                <input type="text" id="{{ $id }}" class="form-control form-control-sm rounded-sm" placeholder="{{ $placeholder }}" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
            </div>
            
            @if($showStatusFilter)
            <div class="col-md-3">
                <label class="form-label" style="font-size: 0.65rem;">Status</label>
                <select id="statusFilter" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                    <option value="">All</option>
                    @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            
            @if($showCategoryFilter)
            <div class="col-md-3">
                <label class="form-label" style="font-size: 0.65rem;">Category</label>
                <select id="categoryFilter" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                    <option value="">All</option>
                    @foreach($categoryOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            
            @if($showTypeFilter)
            <div class="col-md-3">
                <label class="form-label" style="font-size: 0.65rem;">Type</label>
                <select id="typeFilter" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                    <option value="">All</option>
                    @foreach($typeOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            
            <div class="col-md-2">
                <button id="resetFilters" class="btn btn-outline-secondary btn-sm w-100 rounded-sm" style="font-size: 0.65rem; padding: 0.25rem 0.3rem;">
                    <i class="ti ti-refresh me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>