{{--
    ajax=true switches this into a mode that plays nicely with initLiveSearch()
    (see layouts/master.blade.php): page links get a data-page attribute for
    initLiveSearch's delegated click handler to pick up instead of navigating,
    and the per-page <select> skips the plain full-reload listener below
    (initLiveSearch's own delegated 'change' listener handles it instead).
    Default stays false so every existing non-search page using this
    component keeps behaving exactly as before.

    id/pageParam/perPageParam let two of these coexist on one page (e.g. two
    independently-paginated tables): pass a distinct `id` so the per-page
    <select> gets a unique DOM id instead of colliding on "perPageSelect",
    and pass `pageParam`/`perPageParam` matching the page name given to the
    paginator's own paginate() call so the reload keeps both lists' state.
--}}
@props([
    'items',
    'perPageOptions' => [10, 25, 50, 100],
    'ajax' => false,
    'id' => null,
    'pageParam' => 'page',
    'perPageParam' => 'per_page',
])

@php
    $selectId = $id ? 'perPageSelect_'.$id : 'perPageSelect';
@endphp

@if($items->total() > 0)
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted" style="font-size: 0.65rem;">Show</span>
        <select id="{{ $selectId }}" class="form-select form-select-sm rounded-sm" style="width: 70px; font-size: 0.7rem; padding: 0.2rem 0.3rem;">
            @foreach($perPageOptions as $option)
            <option value="{{ $option }}" {{ request($perPageParam, $items->perPage()) == $option ? 'selected' : '' }}>{{ $option }}</option>
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
                            <a class="page-link" href="{{ $items->url(1) }}" @if($ajax) data-page="1" @endif style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">«</a>
                        </li>
                    @endif

                    {{-- Previous Page --}}
                    @if($items->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">‹</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $items->previousPageUrl() }}" @if($ajax) data-page="{{ $items->currentPage() - 1 }}" @endif style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">‹</a>
                        </li>
                    @endif

                    {{-- Page Numbers (show only 5 pages around current) --}}
                    @php
                        $start = max(1, $items->currentPage() - 2);
                        $end = min($items->lastPage(), $items->currentPage() + 2);
                        $pageAttr = fn ($page) => $ajax ? ' data-page="'.$page.'"' : '';

                        if ($start > 1) {
                            echo '<li class="page-item"><a class="page-link" href="' . $items->url(1) . '"' . $pageAttr(1) . ' style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">1</a></li>';
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
                                <a class="page-link" href="{{ $items->url($i) }}" @if($ajax) data-page="{{ $i }}" @endif style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">{{ $i }}</a>
                            </li>
                        @endif
                    @endfor

                    @php
                        if ($end < $items->lastPage()) {
                            if ($end < $items->lastPage() - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            echo '<li class="page-item"><a class="page-link" href="' . $items->url($items->lastPage()) . '"' . $pageAttr($items->lastPage()) . ' style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">' . $items->lastPage() . '</a></li>';
                        }
                    @endphp

                    {{-- Next Page --}}
                    @if($items->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $items->nextPageUrl() }}" @if($ajax) data-page="{{ $items->currentPage() + 1 }}" @endif style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">›</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link" style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">›</span>
                        </li>
                    @endif

                    {{-- Last Page --}}
                    @if($items->currentPage() < $items->lastPage() - 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ $items->url($items->lastPage()) }}" @if($ajax) data-page="{{ $items->lastPage() }}" @endif style="padding: 0.2rem 0.4rem; font-size: 0.65rem;">»</a>
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

@unless($ajax)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const perPageSelect = document.getElementById('{{ $selectId }}');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('{{ $perPageParam }}', this.value);
            url.searchParams.set('{{ $pageParam }}', 1); // Reset to first page
            window.location.href = url.toString();
        });
    }
});
</script>
@endunless
@else
<div class="text-center py-2 text-muted" style="font-size: 0.7rem;">
    <i class="ti ti-database me-1"></i> No entries found
</div>
@endif