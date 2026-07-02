@props([
    'viewRoute' => null,
    'editRoute' => null,
    'deleteRoute' => null,
    'cancelRoute' => null,
    'cancelId' => null,
    'cancelName' => null,
    'deleteId' => null,
    'deleteName' => null,
    'viewId' => null,
    'showCancel' => false,
    'showDelete' => false,
])

<div class="btn-group" role="group">
    @if($viewRoute)
    <a href="{{ $viewRoute }}" class="btn btn-sm btn-outline-info rounded-sm" title="View" style="padding: 0.15rem 0.3rem;">
        <i class="ti ti-eye" style="font-size: 0.7rem;"></i>
    </a>
    @endif
    
    @if($editRoute)
    <a href="{{ $editRoute }}" class="btn btn-sm btn-outline-primary rounded-sm" title="Edit" style="padding: 0.15rem 0.3rem;">
        <i class="ti ti-edit" style="font-size: 0.7rem;"></i>
    </a>
    @endif
    
    @if($showCancel && $cancelRoute)
    <button type="button" class="btn btn-sm btn-outline-warning rounded-sm" onclick="showCancelModal('{{ $cancelId }}', '{{ $cancelName }}')" title="Cancel Request" style="padding: 0.15rem 0.3rem;">
        <i class="ti ti-x" style="font-size: 0.7rem;"></i>
    </button>
    @endif
    
    @if($showDelete && $deleteRoute)
    <button type="button" class="btn btn-sm btn-outline-danger rounded-sm" onclick="showDeleteModal('{{ $deleteId }}', '{{ $deleteName }}')" title="Delete" style="padding: 0.15rem 0.3rem;">
        <i class="ti ti-trash" style="font-size: 0.7rem;"></i>
    </button>
    @endif
</div>