@props([
    'name' => null,
    'id' => null,
    'label' => '',
    'icon' => null,
    'options' => [],
    'placeholder' => 'Select...',
    'required' => false,
    'disabled' => false,
    'value' => null,
    'helpText' => null,
    // null = auto: a search box appears once the list is long enough to need
    // one (or starts empty and gets filled by JS); true/false forces it.
    'searchable' => null,
])

{{--
    A closed/compact button that opens a Bootstrap dropdown-menu capped at
    ~5 visible rows with scroll - the custom control this app uses instead
    of a native <select> whenever the option list can run long (a native
    select's open popup can't be height-limited with CSS). JS wiring
    (selection, cascading, old()-value restore) lives once in
    the global wireDropdown() helper this component ships once per page
    (see the @once block at the bottom), which this component's element IDs
    ({id}, {id}_toggle, {id}_menu) are built to match exactly.

    A search box at the top of the open panel filters the options as you type
    (Enter picks the first match), so a list of hundreds stays quick to use.

    Pass $options as an array of ['value' => .., 'label' => .., 'title' => optional].
    Leave $options empty for a dropdown that starts empty and is populated
    by JS later (e.g. Programme, cascaded from Faculty/School).
--}}
@php
    $selectedValue = old($name ?? '', $value);
    $selectedLabel = collect($options)->first(fn ($option) => (string) $option['value'] === (string) $selectedValue)['label'] ?? null;
    $showSearch = $searchable ?? (count($options) > 7 || count($options) === 0);
@endphp
@if($label)
<label class="form-label" style="font-size: 0.65rem;">
    @if($icon)<i class="ti {{ $icon }} me-1 text-muted"></i>@endif{{ $label }} @if($required)<span class="text-danger">*</span>@endif
</label>
@endif
<div class="dropdown">
    <button type="button"
            id="{{ $id }}_toggle"
            class="form-select form-select-sm rounded-sm text-start {{ $errors->has($name ?? '') ? 'is-invalid' : '' }}"
            data-bs-toggle="dropdown"
            @if($disabled) disabled @endif>
        {{ $selectedLabel ?? $placeholder }}
    </button>
    <div class="dropdown-menu w-100 p-0">
        @if($showSearch)
        <div class="p-1 border-bottom">
            <input type="search" class="form-control form-control-sm rounded-sm dropdown-search-input" placeholder="Search..." autocomplete="off" aria-label="Search options" style="font-size: 0.7rem;">
        </div>
        @endif
        <ul class="list-unstyled mb-0 scrollable-dropdown-menu" id="{{ $id }}_menu">
            @foreach($options as $option)
                <li>
                    <button type="button" class="dropdown-item" data-value="{{ $option['value'] }}" @if(!empty($option['title'])) title="{{ $option['title'] }}" @endif>
                        {{ $option['label'] }}
                    </button>
                </li>
            @endforeach
        </ul>
        <div class="dropdown-search-empty d-none text-muted px-3 py-2" style="font-size: 0.7rem;">No matches</div>
    </div>
    <input type="hidden" @if($name) name="{{ $name }}" @endif id="{{ $id }}" value="{{ old($name ?? '', $value) }}">
</div>
@error($name ?? '')<div class="invalid-feedback d-block" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
@if($helpText)<small class="text-muted" style="font-size: 0.6rem;">{{ $helpText }}</small>@endif

@once
<style>
/* Native <select> can't cap its open popup to a handful of rows - these are
   plain buttons + a Bootstrap dropdown-menu instead, so the closed state
   stays a compact single line and the open panel only ever shows about 5
   rows before scrolling, however long the real list is. */
.scrollable-dropdown-menu { max-height: 168px; overflow-y: auto; }
.scrollable-dropdown-menu .dropdown-item { white-space: normal; font-size: 0.7rem; padding: 0.3rem 0.75rem; }
</style>
<script>
/* Every x-scrollable-dropdown updates its own hidden <input> (the real form
   value) and toggle <button> (the visible "closed" label) on selection -
   no per-page wiring needed. Runs in the capture phase so the value is
   already set by the time any page-level click listener on the menu reads
   it. Menu/toggle/input are found from the {id}_menu naming convention. */
document.addEventListener('click', function (event) {
    const item = event.target.closest('.scrollable-dropdown-menu .dropdown-item');
    if (!item) {
        return;
    }
    const baseId = item.closest('.scrollable-dropdown-menu').id.replace(/_menu$/, '');
    const hiddenInput = document.getElementById(baseId);
    const toggle = document.getElementById(baseId + '_toggle');
    if (hiddenInput) {
        hiddenInput.value = item.dataset.value;
    }
    if (toggle) {
        toggle.textContent = item.textContent.trim();
    }
}, true);

/* Search box: filters the options of the panel it sits in. Reset and focused
   each time the dropdown opens, so it also works for lists that JS refills. */
function filterDropdownOptions(input) {
    const panel = input.closest('.dropdown-menu');
    const query = input.value.trim().toLowerCase();
    let visible = 0;
    panel.querySelectorAll('.scrollable-dropdown-menu .dropdown-item').forEach(function (item) {
        const matches = !query || item.textContent.toLowerCase().includes(query);
        item.closest('li').classList.toggle('d-none', !matches);
        if (matches) {
            visible++;
        }
    });
    panel.querySelector('.dropdown-search-empty').classList.toggle('d-none', visible > 0);
}

document.addEventListener('input', function (event) {
    if (event.target.matches('.dropdown-search-input')) {
        filterDropdownOptions(event.target);
    }
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Enter' && event.target.matches('.dropdown-search-input')) {
        event.preventDefault();
        const first = event.target.closest('.dropdown-menu').querySelector('li:not(.d-none) .dropdown-item');
        if (first) {
            first.click();
        }
    }
});

document.addEventListener('shown.bs.dropdown', function (event) {
    const input = event.target.closest('.dropdown')?.querySelector('.dropdown-search-input');
    if (input) {
        input.value = '';
        filterDropdownOptions(input);
        input.focus();
    }
});

/* Only needed by pages that must react to a selection (onSelect) or clear /
   repopulate a dropdown (reset), e.g. Faculty -> Programme cascades. */
function wireDropdown(menuId, hiddenInputId, toggleId, placeholder, onSelect) {
    const menu = document.getElementById(menuId);
    const hiddenInput = document.getElementById(hiddenInputId);
    const toggle = document.getElementById(toggleId);

    menu.addEventListener('click', function (event) {
        const item = event.target.closest('.dropdown-item');
        if (!item) {
            return;
        }
        hiddenInput.value = item.dataset.value;
        toggle.textContent = item.textContent.trim();
        if (onSelect) {
            onSelect(item.dataset.value);
        }
    });

    return {
        reset: function (text) {
            hiddenInput.value = '';
            toggle.textContent = text ?? placeholder;
        },
    };
}
</script>
@endonce
