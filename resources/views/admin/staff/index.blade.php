@extends('layouts.master')

@section('title', 'Staff Management')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Staff Management', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="STAFF MANAGEMENT" description="Manage staff members, roles, and permissions">
        {{-- Only show Add and Import buttons for admin, HR, or Dean roles --}}
        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager') || auth()->user()->hasRole('dean'))
        <a href="{{ route('staff.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Add Staff
        </a>
        <a href="{{ route('admin.import.staff') }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-file-import me-1"></i> Import
        </a>
        @endif
    </x-page-header>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $totalStaff ?? $staffMembers->total() }}" label="Total Staff" subtext="↑ +{{ $newThisMonth ?? 0 }} this month" />
        <x-stats-card value="{{ $academicStaff ?? 0 }}" label="Academic" subtext="teaching staff" color="primary" />
        <x-stats-card value="{{ $administrativeStaff ?? 0 }}" label="Administrative" subtext="office staff" color="primary" />
        <x-stats-card value="{{ $supportingStaff ?? 0 }}" label="Supporting" subtext="technical & support" color="primary" />
    </div>
    
    <!-- Alerts -->
    <x-alert-success />
    
    <x-alert-danger />
    
    <!-- Search Card -->
    <div id="searchCard">
        <x-search-card placeholder="Search by name, email or staff ID..." id="searchInput" name="search" :value="request('search')">
            <div id="searchStats" class="text-muted" style="font-size: 0.65rem;">
                {{ $staffMembers->total() }} staff total
            </div>
        </x-search-card>
    </div>

    <div class="row g-2">
        <!-- Staff Table - main column -->
        <div class="col-lg-8">
            <div id="staffResults">
                @include('admin.staff._results')
            </div>
        </div>

        <!-- Information - narrow column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-users me-1 text-primary"></i> About Staff</div>
                        <p class="mb-0" style="font-size: 0.65rem; color: #555;">Staff records cover academic, administrative and supporting personnel. Each staff member can hold roles and positions used across leave approvals, ID cards and gate access.</p>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('admin.import.staff') }}">Import Staff</a> - bulk upload from Excel</li>
                            <li><a href="{{ route('admin.roles.index') }}">Roles</a> - manage roles and assignments</li>
                            <li><a href="{{ route('admin.departments.index') }}">Departments</a> - manage staff's departments</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Search matches name, email or staff ID</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Only Admin, HR or Dean roles can add or import staff</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Pagination Styles */
.custom-pagination {
    margin: 0;
}

.pagination-links {
    display: flex;
    gap: 0.25rem;
    align-items: center;
}

.pagination-item {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    padding: 0 0.4rem;
    font-size: 0.65rem;
    color: #475569;
    background: white;
    border: 1px solid #E8E2D5;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.pagination-item:hover:not(.disabled):not(.active) {
    background: #F1F5F9;
    border-color: #CBD5E1;
    color: #1E293B;
}

.pagination-item.active {
    background: #3B82F6;
    border-color: #3B82F6;
    color: white;
}

.pagination-item.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #F8FAFC;
}

.pagination-item.dots {
    border: none;
    background: transparent;
    cursor: default;
}

/* Per page select styling */
.custom-select-sm {
    font-size: 0.65rem;
    padding: 0.2rem 1.2rem 0.2rem 0.4rem;
    border: 1px solid #E8E2D5;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}

.custom-select-sm:focus {
    outline: none;
    border-color: #3B82F6;
}

/* Action buttons hover effects */
.btn-outline-info:hover i,
.btn-outline-primary:hover i,
.btn-outline-danger:hover i {
    color: white;
}
</style>

<script>
// Live search — fetches filtered results in place instead of doing a full
// page navigation, so it never interrupts you (including when you clear the
// box back to empty) and can search the whole staff list, not just whatever
// happens to be on the current page.
(function () {
    const staffIndexUrl = '{{ route('staff.index') }}';
    const resultsEl = document.getElementById('staffResults');
    const statsEl = document.getElementById('searchStats');
    const searchInput = document.getElementById('searchInput');
    const suggestionsEl = document.getElementById('searchInputSuggestions');
    const recentSearchesKey = 'staffRecentSearches';
    let perPage = {{ $staffMembers->perPage() }};
    let currentPage = 1;
    let debounceTimer;
    let requestSeq = 0;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function getRecentSearches() {
        try {
            return JSON.parse(localStorage.getItem(recentSearchesKey)) || [];
        } catch (e) {
            return [];
        }
    }

    function saveRecentSearch(term) {
        term = term.trim();
        if (term === '') {
            return;
        }
        let recent = getRecentSearches().filter(t => t.toLowerCase() !== term.toLowerCase());
        recent.unshift(term);
        recent = recent.slice(0, 5);
        localStorage.setItem(recentSearchesKey, JSON.stringify(recent));
    }

    function renderSuggestions() {
        if (!suggestionsEl) {
            return;
        }
        const recent = getRecentSearches();
        if (recent.length === 0) {
            suggestionsEl.classList.add('d-none');
            return;
        }
        suggestionsEl.innerHTML = recent.map(term =>
            `<div class="search-suggestion-item" data-term="${escapeHtml(term)}"><i class="ti ti-history"></i> ${escapeHtml(term)}</div>`
        ).join('');
        suggestionsEl.classList.remove('d-none');
    }

    function hideSuggestions() {
        suggestionsEl?.classList.add('d-none');
    }

    function load(page = 1) {
        currentPage = page;
        const term = searchInput.value.trim();
        const params = new URLSearchParams();
        if (term !== '') {
            params.set('search', term);
        }
        params.set('per_page', perPage);
        params.set('page', page);

        const seq = ++requestSeq;

        fetch(staffIndexUrl + '?' + params.toString(), {
            headers: { 'Accept': 'application/json' },
        })
            .then(response => response.json())
            .then(data => {
                if (seq !== requestSeq) {
                    return; // a newer request already landed, ignore this stale one
                }
                resultsEl.innerHTML = data.html;
                if (statsEl) {
                    const blue = n => `<span style="color:#3B82F6; font-weight:700;">${n}</span>`;
                    statsEl.innerHTML = term !== ''
                        ? `${blue(data.total)} of ${blue(data.grand_total)} staff match "${escapeHtml(term)}"`
                        : `${blue(data.grand_total)} staff total`;
                }
                history.replaceState(null, '', staffIndexUrl + '?' + params.toString());
                if (term !== '') {
                    saveRecentSearch(term);
                }
            });
    }

    searchInput.addEventListener('keyup', function (e) {
        if (e.key === 'Escape') {
            hideSuggestions();
            return;
        }
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => load(1), 400);
    });

    searchInput.addEventListener('focus', renderSuggestions);

    // Delay so a click on a suggestion registers before the list disappears.
    searchInput.addEventListener('blur', () => setTimeout(hideSuggestions, 150));

    suggestionsEl?.addEventListener('mousedown', function (e) {
        const item = e.target.closest('[data-term]');
        if (item) {
            searchInput.value = item.dataset.term;
            hideSuggestions();
            clearTimeout(debounceTimer);
            load(1);
        }
    });

    resultsEl.addEventListener('click', function (e) {
        const pageLink = e.target.closest('[data-page]');
        if (pageLink) {
            e.preventDefault();
            load(parseInt(pageLink.dataset.page, 10));
        }
    });

    resultsEl.addEventListener('change', function (e) {
        if (e.target.id === 'perPageSelect') {
            perPage = e.target.value;
            load(1);
        }
    });

    document.getElementById('resetFilters')?.addEventListener('click', function (e) {
        e.preventDefault();
        searchInput.value = '';
        load(1);
    });
})();
</script>
@endsection