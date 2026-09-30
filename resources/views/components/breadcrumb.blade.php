@props(['items' => []])

<nav aria-label="breadcrumb" class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
    <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Home</a></li>
        @foreach($items as $item)
            @if($loop->last)
                <li class="breadcrumb-item active">{{ $item['name'] }}</li>
            @else
                <li class="breadcrumb-item"><a href="{{ $item['url'] }}" class="text-muted">{{ $item['name'] }}</a></li>
            @endif
        @endforeach
    </ol>
    <x-academic-year-badge />
</nav>