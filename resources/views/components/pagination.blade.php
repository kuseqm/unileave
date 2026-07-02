@props(['items', 'perPageOptions' => [10, 25, 50, 100]])

@if($items->total() > 0)
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted" style="font-size: 0.65rem;">Show</span>
        <select id="perPageSelect" class="form-select form-select-sm rounded-sm" style="width: 70px; font-size: 0.7rem; padding: 0.2rem 0.3rem;">
            @foreach($perPageOptions as $option)
            <option value="{{ $option }}" {{ request('per_page', $items->perPage()) == $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
        <span class="text-muted" style="font-size: 0.65rem;">entries</span>
    </div>
    
    <div>
        @if($items->lastPage() > 1)
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    {{-- First Page --}}
                    @if($items->currentPage() > 2)
                        <li class="page-item">
                            <a class="page-link" href="{{ $items->url(1) }}" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">«</a>
                        </li>
                    @endif
                    
                    {{-- Previous Page --}}
                    @if($items->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">‹</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $items->previousPageUrl() }}" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">‹</a>
                        </li>
                    @endif
                    
                    {{-- Page Numbers (show only 5 pages around current) --}}
                    @php
                        $start = max(1, $items->currentPage() - 2);
                        $end = min($items->lastPage(), $items->currentPage() + 2);
                        
                        if ($start > 1) {
                            echo '<li class="page-item"><a class="page-link" href="' . $items->url(1) . '" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">1</a></li>';
                            if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    @endphp
                    
                    @for($i = $start; $i <= $end; $i++)
                        @if($i == $items->currentPage())
                            <li class="page-item active">
                                <span class="page-link" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">{{ $i }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $items->url($i) }}" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">{{ $i }}</a>
                            </li>
                        @endif
                    @endfor
                    
                    @php
                        if ($end < $items->lastPage()) {
                            if ($end < $items->lastPage() - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            echo '<li class="page-item"><a class="page-link" href="' . $items->url($items->lastPage()) . '" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">' . $items->lastPage() . '</a></li>';
                        }
                    @endphp
                    
                    {{-- Next Page --}}
                    @if($items->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $items->nextPageUrl() }}" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">›</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">›</span>
                        </li>
                    @endif
                    
                    {{-- Last Page --}}
                    @if($items->currentPage() < $items->lastPage() - 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ $items->url($items->lastPage()) }}" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">»</a>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
    </div>
    
    <div class="text-muted" style="font-size: 0.65rem;">
        <i class="ti ti-database me-1"></i> 
        Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} entries
    </div>
</div>

<style>
.pagination {
    margin-bottom: 0;
    flex-wrap: wrap;
    gap: 0.125rem;
}
.pagination .page-link {
    color: #475569;
    background-color: white;
    border: 1px solid #E2E8F0;
    border-radius: 0.25rem;
}
.pagination .page-link:hover {
    background-color: #F1F5F9;
    color: #1E293B;
}
.pagination .page-item.active .page-link {
    background-color: #3B82F6;
    border-color: #3B82F6;
    color: white;
}
.pagination .page-item.disabled .page-link {
    color: #94A3B8;
    background-color: #F8FAFC;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.set('page', 1); // Reset to first page
            window.location.href = url.toString();
        });
    }
});
</script>
@else
<div class="text-center py-2 text-muted" style="font-size: 0.7rem;">
    <i class="ti ti-database me-1"></i> No entries found
</div>
@endif