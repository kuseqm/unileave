@props(['title', 'description', 'backUrl' => null])

<div class="card rounded-sm mb-2">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="h5 mb-0" style="color: #3B82F6; font-weight: 700;">{{ $title }}</h1>
                <p class="text-muted mb-0" style="font-size: 0.7rem;">{{ $description }}</p>
            </div>
            <div class="d-flex gap-2">
                @if($backUrl)
                <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                    <i class="ti ti-arrow-left me-1"></i> Back
                </a>
                @endif
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
