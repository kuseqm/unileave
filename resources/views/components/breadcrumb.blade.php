@props(['items' => []])

<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-2" style="font-size: 0.75rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Home</a></li>
        @foreach($items as $item)
            @if($loop->last)
                <li class="breadcrumb-item active">{{ $item['name'] }}</li>
            @else
                <li class="breadcrumb-item"><a href="{{ $item['url'] }}" class="text-muted">{{ $item['name'] }}</a></li>
            @endif
        @endforeach
    </ol>
</nav>