<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ app_abbreviation() }} | @yield('title')</title>
    
    <!-- Bootstrap CSS - self-hosted, not the jsdelivr CDN. These are
         render-blocking <link> tags in <head>, so a slow CDN response here
         holds up first paint of everything on the page, including the
         page-transition loader. -->
    <link href="{{ asset('css/vendor/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Tabler Icons - self-hosted (css + woff2/woff/ttf), same reason. -->
    <link rel="stylesheet" href="{{ asset('css/vendor/tabler-icons.min.css') }}">
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    
    <!-- Ember System Standards -->
    <link rel="stylesheet" href="{{ asset('css/ember-standards.css') }}">

    {{-- Self-hosted, not the code.jquery.com CDN - that CDN was taking 2-3
         seconds to respond from this environment, and since this is a
         render-blocking <script> tag, the whole page sat blank that long
         on every single navigation. Local file removes the network hop
         entirely. --}}
    <script src="{{ asset('js/vendor/jquery-3.6.0.min.js') }}"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Cormorant+Garamond:wght@400;600;700&family=Cinzel:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Layout Container */
        .app-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        
        /* Sidebar Area - FLEX COLUMN */
        .sidebar-area {
            width: 260px;
            background: #fdf8f4 !important;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100vh;
            flex-shrink: 0;
        }
        
        .sidebar-area.collapsed {
            width: 70px;
        }
        
        /* Content Area */
        .content-area {
            flex: 1;
            background: #fdf8f4 !important;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow-y: auto;
        }
        
        /* Sidebar Top Bar - FIXED */
        .sidebar-topbar {
            height: 60px;
            background: #fdf8f4 !important;
            border-bottom: 1px solid #E8E2D5;
            display: flex;
            align-items: center;
            padding: 0 1rem;
            flex-shrink: 0;
        }
        
        /* Circular Toggle Button */
        .sidebar-toggle {
            width: 32px;
            height: 32px;
            background: #C4D9EE;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #475569;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.2s;
            margin-right: 12px;
        }
        
        .sidebar-toggle:hover {
            background: #B0C8E0;
            color: #1E293B;
        }
        
        /* Sidebar Logo Area */
        .sidebar-logo {
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-logo h3 {
            color: #1E293B;
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
            line-height: 1.2;
        }
        
        .sidebar-logo .logo-blue {
            color: #3B82F6;
            font-weight: 700;
        }
        
        .sidebar-logo small {
            color: #64748B;
            font-size: 0.65rem;
            letter-spacing: 1px;
        }
        
        /* Main Content */
        .main-content {
            padding: 1rem;
            flex: 1;
            background: #fdf8f4 !important;
        }
        
        /* Card Styles */
        .card {
            border: 1px solid #E8E2D5;
            background: white;
            margin-bottom: 0.75rem;
        }
        
        .card-header {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #E8E2D5;
            background: transparent;
            font-weight: 600;
        }
        
        .card-body {
            padding: 0.5rem 0.75rem;
        }
        
        /* When sidebar collapsed */
        .sidebar-area.collapsed .sidebar-logo h3,
        .sidebar-area.collapsed .sidebar-logo small {
            display: none;
        }
        
        .sidebar-area.collapsed .sidebar-topbar {
            justify-content: center;
        }
        
        .sidebar-area.collapsed .sidebar-toggle {
            margin-right: 0;
        }
        
        /* Sticky Banner Styles */
        .sticky-banner {
            position: sticky;
            top: 60px;
            z-index: 98;
            /* Same amber as before, just translucent + blurred so the
               page content scrolling underneath shows through frosted
               instead of a flat block of color. */
            background: rgba(254, 243, 199, 0.6);
            backdrop-filter: blur(10px) saturate(160%);
            -webkit-backdrop-filter: blur(10px) saturate(160%);
            border-bottom: 1px solid rgba(245, 158, 11, 0.45);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5);
            padding: 0.5rem 1rem;
            animation: slideDown 0.3s ease-out;
            flex-shrink: 0;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar-area {
                position: fixed;
                z-index: 1000;
                transform: translateX(-100%);
            }
            .sidebar-area.show {
                transform: translateX(0);
            }
            .content-area {
                width: 100%;
            }

            .sticky-banner {
                padding: 0.5rem 0.75rem;
            }

            .sticky-banner .small {
                font-size: 0.7rem;
            }

            /* Small action buttons (Back, Print, PDF, etc.) sit next to a
               page title that can wrap to 2 lines on a phone - without this,
               the button's own icon+label wraps internally too and turns
               into an ugly 2-line box instead of staying a compact pill. */
            .btn-sm {
                white-space: nowrap;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>

    <!-- Page-transition loader: only for genuinely slow navigations (see
         REVEAL_DELAY_MS in the script below) - fast pages never show this
         at all. Design: comet (option 6 from the mockups -
         https://claude.ai/code/artifact/0723d2c4-7d83-4c35-9e7f-24251f093eed)
         - a rotating gradient arc with a static "SJUT" badge fixed at
         center. The departing page only sets a sessionStorage flag (see the
         script below) - it never shows anything itself and never delays
         navigation. -->
    <div id="pageLoaderOverlay">
        <div id="pageLoaderComet">
            <svg id="pageLoaderRing" viewBox="0 0 88 88">
                <defs>
                    <linearGradient id="pageLoaderGradient" gradientUnits="userSpaceOnUse" x1="44.4" y1="85" x2="85" y2="44">
                        <stop offset="0%" stop-color="#7DD3FC" stop-opacity="1" />
                        <stop offset="100%" stop-color="#3B82F6" stop-opacity="0" />
                    </linearGradient>
                </defs>
                <circle cx="44" cy="44" r="41" fill="none" stroke="url(#pageLoaderGradient)" stroke-width="3" stroke-linecap="round" stroke-dasharray="64 194" />
                <circle cx="44.4" cy="85" r="2.6" fill="#7DD3FC" stroke="none" />
            </svg>
            <div id="pageLoaderBadge">SJUT</div>
        </div>
    </div>
    <style>
        #pageLoaderOverlay {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(253, 248, 244, 0.94);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        #pageLoaderOverlay.is-visible {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        #pageLoaderComet {
            position: relative;
            width: 64px;
            height: 64px;
        }
        #pageLoaderRing {
            display: block;
            width: 100%;
            height: 100%;
            /* Runs continuously from page load, visible or not - see the
               script below for why display:none is never used to hide this. */
            animation: pageLoaderRot 1.3s linear infinite;
            /* Hints the browser to composite this rotation on its own GPU
               layer up front, so it isn't competing with whatever else is
               happening on the main thread (e.g. the notification/message
               badge polling) for a chance to paint smoothly. */
            will-change: transform;
        }
        #pageLoaderBadge {
            position: absolute;
            inset: 0;
            margin: auto;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 0 0 1px #E8E2D5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.55rem;
            font-weight: 800;
            color: #3B82F6;
            letter-spacing: -0.02em;
        }
        @keyframes pageLoaderRot { to { transform: rotate(360deg); } }
    </style>
    <script>
        (function () {
            var overlay = document.getElementById('pageLoaderOverlay');
            var FLAG = 'pageLoaderActive';
            // Only reveal the loader once a navigation has genuinely been
            // running this long - most pages here load well under this, so
            // ordinary navigations show nothing at all, and only pages that
            // are actually heavy (a big report, an import, a slow query)
            // ever surface the loader. This is deliberately automatic
            // rather than a per-page/per-route flag - no page needs to be
            // manually marked "heavy"; if it happens to take a while, the
            // loader appears on its own, and if a normally-fast page is
            // slow for some other reason (server hiccup, etc.) it still
            // gets covered.
            var REVEAL_DELAY_MS = 150;
            var revealTimer = null;
            var safetyTimer = null;
            var loaded = false;

            function show() {
                overlay.classList.add('is-visible');
                // Belt-and-braces: if nothing ever calls hide() - the user
                // hits Stop, a request errors out without completing the
                // navigation, a script elsewhere throws before
                // DOMContentLoaded fires - the overlay would otherwise sit
                // there forever.
                clearTimeout(safetyTimer);
                safetyTimer = setTimeout(function () {
                    sessionStorage.removeItem(FLAG);
                    hide();
                }, 10000);
            }
            function hide() {
                overlay.classList.remove('is-visible');
                clearTimeout(safetyTimer);
                clearTimeout(revealTimer);
            }

            if (sessionStorage.getItem(FLAG) === '1') {
                sessionStorage.removeItem(FLAG);
                revealTimer = setTimeout(function () {
                    if (!loaded) {
                        show();
                    }
                }, REVEAL_DELAY_MS);
            }

            // DOMContentLoaded, not 'load' - 'load' waits for every image,
            // icon font and other background resource, which can finish
            // well after the page is already visible and usable. Hiding on
            // DOMContentLoaded means the loader gets out of the way the
            // moment the actual content is ready, not whenever the slowest
            // background resource happens to finish. This still can't cause
            // a flash of unstyled content - a render-blocking stylesheet in
            // <head> (all of ours are) delays DOMContentLoaded until it has
            // been applied, same as it always has.
            document.addEventListener('DOMContentLoaded', function () {
                loaded = true;
                hide();
            });

            // A page restored from the back/forward cache (bfcache) never
            // fires DOMContentLoaded again - only 'pageshow' with
            // persisted === true - so without this, a still-pending reveal
            // timer could fire on a page that finished loading long ago.
            window.addEventListener('pageshow', function (e) {
                if (e.persisted) {
                    loaded = true;
                    hide();
                }
            });

            // Just mark that a same-origin navigation is under way - no
            // visual action here, and nothing preventDefault'd or delayed.
            // The link/form behaves exactly as if this script didn't exist;
            // the arriving page alone decides, after its own grace period,
            // whether the load was fast enough to skip showing anything.
            function announce() {
                sessionStorage.setItem(FLAG, '1');
            }

            document.addEventListener('click', function (e) {
                var link = e.target.closest('a[href]');
                if (!link || e.defaultPrevented) {
                    return;
                }
                if (link.target === '_blank' || link.hasAttribute('download')) {
                    return;
                }
                var href = link.getAttribute('href');
                if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0 || href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) {
                    return;
                }
                try {
                    if (new URL(link.href, window.location.href).origin !== window.location.origin) {
                        return;
                    }
                } catch (err) {
                    return;
                }
                announce();
            });

            // Regular form submits navigate away too - cover those the same way.
            document.addEventListener('submit', function (e) {
                if (!e.defaultPrevented) {
                    announce();
                }
            });

            // Catches everything the listeners above don't (back/forward,
            // address bar, programmatic redirects).
            window.addEventListener('beforeunload', announce);
        })();
    </script>

    <div class="app-container">

        <!-- Sidebar Area -->
        <div class="sidebar-area" id="sidebarArea">
            <!-- Sidebar Top Bar - FIXED AT TOP -->
            <div class="sidebar-topbar">
                <div class="sidebar-toggle" id="sidebarToggle">
                    <i class="ti ti-chevron-left" id="toggleIcon"></i>
                </div>
                <div class="sidebar-logo">
                    <h3>Staff<span class="logo-blue">MS</span></h3>
                    <small>STAFF MANAGEMENT</small>
                </div>
            </div>
            
            <!-- Sidebar Navigation - SCROLLABLE AREA -->
            <div class="sidebar-nav-container">
                @include('layouts.partials.sidebar')
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
            <!-- Content Top Bar - NOW A COMPONENT -->
            @auth
            <x-topbar />
            @endauth
            
            <!-- ============================================ -->
            <!-- STICKY BANNER WITH APPROVAL TRACKER (ADDED) -->
            <!-- ============================================ -->
            @auth
            @php
                $pendingRequest = Auth::user()->leaveRequests()
                    ->where('status', 'pending')
                    ->where('acting_status', '!=', 'declined')
                    ->latest()
                    ->first();
            @endphp
            
            @if($pendingRequest)
            <div class="sticky-banner">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <i class="ti ti-alert-triangle" style="color: #D97706; font-size: 1.2rem;"></i>
                        <span class="small fw-medium d-none d-sm-inline" style="color: #92400E;">Your leave request is pending approval</span>
                        <span class="small fw-medium d-inline d-sm-none" style="color: #92400E;">Request progress</span>
                        <x-approval-tracker-mini :leaveRequest="$pendingRequest" />
                    </div>
                    <a href="{{ route('leave-requests.show', $pendingRequest->id) }}" class="small text-decoration-none" style="color: #D97706; font-weight: 500;">
                        <span class="d-none d-sm-inline">View Details</span><span class="d-inline d-sm-none">Details</span> <i class="ti ti-arrow-right"></i>
                    </a>
                </div>
            </div>
            @endif
            @endauth
            
            <!-- Main Content -->
            <div class="main-content">
                <x-alert-success />
                <x-alert-danger />
                @yield('content')
            </div>
        </div>
        
    </div>
    
    <!-- Scripts -->
    <script src="{{ asset('js/vendor/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        // Sidebar Toggle Functionality
        const sidebarArea = document.getElementById('sidebarArea');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const toggleIcon = document.getElementById('toggleIcon');
        
        function updateToggleIcon() {
            if (sidebarArea.classList.contains('collapsed')) {
                toggleIcon.classList.remove('ti-chevron-left');
                toggleIcon.classList.add('ti-chevron-right');
            } else {
                toggleIcon.classList.remove('ti-chevron-right');
                toggleIcon.classList.add('ti-chevron-left');
            }
        }
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebarArea.classList.toggle('collapsed');
                updateToggleIcon();
                localStorage.setItem('sidebarCollapsed', sidebarArea.classList.contains('collapsed'));
            });
        }
        
        // Load saved state
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebarArea.classList.add('collapsed');
            updateToggleIcon();
        }
        
        // Mobile sidebar toggle
        // The button that reveals the sidebar lives in the topbar (always on-screen),
        // not inside the sidebar itself (which starts translated off-screen on mobile
        // and would otherwise trap its own opener). See .mobile-sidebar-toggle in
        // components/topbar.blade.php.
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        if (mobileSidebarToggle) {
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            overlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999; display: none;';
            document.body.appendChild(overlay);

            mobileSidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebarArea.classList.toggle('show');
                overlay.style.display = sidebarArea.classList.contains('show') ? 'block' : 'none';
            });

            overlay.addEventListener('click', function() {
                sidebarArea.classList.remove('show');
                overlay.style.display = 'none';
            });

            // Close the mobile sidebar after a nav link is tapped
            sidebarArea.querySelectorAll('a.nav-link, a.nav-sub-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebarArea.classList.remove('show');
                        overlay.style.display = 'none';
                    }
                });
            });
        }
        
        // Redirect to dashboard if at root
        if (window.location.pathname === '/' || window.location.pathname === '/admin/dashboard') {
            window.location.href = '/dashboard';
        }
    </script>
    
    <!-- Global Delete Modal Function -->
    <script>
    function showDeleteModal(itemId, itemName, itemType, itemRoute) {
        // For modals that are dynamically added or need to be found
        let modal = document.getElementById('deleteModal' + itemId);
        
        // If modal doesn't exist, create a temporary one
        if (!modal) {
            // Create modal HTML with type-specific warnings
            let typeWarning = '';
            
            if (itemType === 'blackout') {
                typeWarning = `
                    <x-alert-info>
    <strong>Impact:</strong><br>
                        - Staff will be able to apply for leave during this period<br>
                        - No leave requests will be affected (only future applications)
</x-alert-info>
                `;
            } else if (itemType === 'university') {
                typeWarning = `
                    <x-alert-danger :session="false">
    <strong>This will permanently delete:</strong><br>
                        - All campuses under this university<br>
                        - All departments under those campuses<br>
                        - All staff assignments to those departments
</x-alert-danger>
                `;
            } else if (itemType === 'school') {
                typeWarning = `
                    <x-alert-danger :session="false">
    <strong>This will permanently delete:</strong><br>
                        - All departments under this school<br>
                        - All staff assignments to those departments
</x-alert-danger>
                `;
            } else if (itemType === 'campus') {
                typeWarning = `
                    <x-alert-danger :session="false">
    <strong>This will permanently delete:</strong><br>
                        - All departments under this campus<br>
                        - All staff assignments to those departments
</x-alert-danger>
                `;
            } else if (itemType === 'department') {
                typeWarning = `
                    <x-alert-danger :session="false">
    <strong>This will permanently delete:</strong><br>
                        - All staff assignments to this department<br>
                        - Staff will need to be reassigned
</x-alert-danger>
                `;
            } else if (itemType === 'leave_type') {
                typeWarning = `
                    <x-alert-warning>
    <strong>Note:</strong><br>
                        - Existing leave requests of this type will NOT be deleted<br>
                        - They will show as "Unknown Type"<br>
                        - Future applications cannot use this type
</x-alert-warning>
                `;
            } else if (itemType === 'notification') {
                typeWarning = `
                    <x-alert-info>
    It will just drop off your list, not gone forever.
</x-alert-info>
                `;
            } else if (itemType === 'staff') {
                typeWarning = `
                    <x-alert-danger :session="false">
    <strong>This will:</strong><br>
                        - Remove all access for this staff member<br>
                        - Archive their leave request history<br>
                        - Remove them from acting staff assignments
</x-alert-danger>
                `;
            } else {
                typeWarning = `
                    <x-alert-danger :session="false">
    This action cannot be undone.
</x-alert-danger>
                `;
            }
            
            const modalHtml = `
                <div class="modal fade" id="deleteModal${itemId}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-sm">
                            <div class="modal-header py-1 px-2 bg-danger-subtle">
                                <h5 class="modal-title" style="font-size: 0.8rem;">
                                    <i class="ti ti-alert-triangle text-danger me-1"></i> Confirm Delete
                                </h5>
                                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body py-2 px-2">
                                <p style="font-size: 0.75rem;">Are you sure you want to delete <strong>${itemName}</strong>?</p>
                                ${typeWarning}
                            </div>
                            <div class="modal-footer py-1 px-2">
                                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">
                                    <i class="ti ti-x me-1"></i> Cancel
                                </button>
                                <form action="${itemRoute}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">
                                        <i class="ti ti-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Append to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            modal = document.getElementById('deleteModal' + itemId);
        }
        
        // Update the form action just in case
        const form = modal.querySelector('form');
        if (form) {
            form.action = itemRoute;
        }
        
        // Update the item name
        const messageSpan = modal.querySelector('.modal-body p');
        if (messageSpan) {
            messageSpan.innerHTML = 'Are you sure you want to delete <strong>' + itemName + '</strong>?';
        }
        
        // Show the modal
        new bootstrap.Modal(modal).show();
    }

    // Shared live-search engine used by list pages (Staff, ID Cards, Audit
    // Logs, Messages, Registry, ...). Fetches filtered results in place via
    // AJAX instead of a full page reload, so typing/paging/reset never
    // navigates away or loses your place — see resources/views/admin/staff/
    // index.blade.php for the original page this was extracted from.
    function initLiveSearch(options) {
        const {
            url,
            resultsSelector,
            statsSelector = null,
            searchInputSelector,
            suggestionsSelector = null,
            resetSelector = '#resetFilters',
            perPage = 15,
            recentSearchesKey = null,
            statLabel = 'records',
            extraParams = () => ({}),
            filterSelectors = [],
            onUpdate = null,
        } = options;

        const resultsEl = document.querySelector(resultsSelector);
        const statsEl = statsSelector ? document.querySelector(statsSelector) : null;
        const searchInput = document.querySelector(searchInputSelector);
        const suggestionsEl = suggestionsSelector ? document.querySelector(suggestionsSelector) : null;

        if (!resultsEl || !searchInput) {
            return null;
        }

        let currentPerPage = perPage;
        let debounceTimer;
        let requestSeq = 0;

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function getRecentSearches() {
            if (!recentSearchesKey) return [];
            try {
                return JSON.parse(localStorage.getItem(recentSearchesKey)) || [];
            } catch (e) {
                return [];
            }
        }

        function saveRecentSearch(term) {
            if (!recentSearchesKey) return;
            term = term.trim();
            if (term === '') return;
            let recent = getRecentSearches().filter(t => t.toLowerCase() !== term.toLowerCase());
            recent.unshift(term);
            recent = recent.slice(0, 5);
            localStorage.setItem(recentSearchesKey, JSON.stringify(recent));
        }

        function renderSuggestions() {
            if (!suggestionsEl) return;
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
            const term = searchInput.value.trim();
            const params = new URLSearchParams(extraParams());
            if (term !== '') {
                params.set('search', term);
            }
            params.set('per_page', currentPerPage);
            params.set('page', page);

            const seq = ++requestSeq;

            fetch(url + '?' + params.toString(), {
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
                        if (term !== '') {
                            statsEl.innerHTML = `${blue(data.total)} of ${blue(data.grand_total)} ${statLabel} match "${escapeHtml(term)}"`;
                        } else if (data.total !== data.grand_total) {
                            statsEl.innerHTML = `${blue(data.total)} of ${blue(data.grand_total)} ${statLabel} match the selected filters`;
                        } else {
                            statsEl.innerHTML = `${blue(data.grand_total)} ${statLabel} total`;
                        }
                    }
                    history.replaceState(null, '', url + '?' + params.toString());
                    if (term !== '') {
                        saveRecentSearch(term);
                    }
                    if (onUpdate) {
                        onUpdate(data, params);
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

        if (suggestionsEl) {
            searchInput.addEventListener('focus', renderSuggestions);
            searchInput.addEventListener('blur', () => setTimeout(hideSuggestions, 150));
            suggestionsEl.addEventListener('mousedown', function (e) {
                const item = e.target.closest('[data-term]');
                if (item) {
                    searchInput.value = item.dataset.term;
                    hideSuggestions();
                    clearTimeout(debounceTimer);
                    load(1);
                }
            });
        }

        resultsEl.addEventListener('click', function (e) {
            const pageLink = e.target.closest('[data-page]');
            if (pageLink) {
                e.preventDefault();
                load(parseInt(pageLink.dataset.page, 10));
            }
        });

        resultsEl.addEventListener('change', function (e) {
            if (e.target.id === 'perPageSelect') {
                currentPerPage = e.target.value;
                load(1);
            }
        });

        filterSelectors.forEach(selector => {
            document.querySelector(selector)?.addEventListener('change', () => load(1));
        });

        document.querySelector(resetSelector)?.addEventListener('click', function (e) {
            e.preventDefault();
            searchInput.value = '';
            filterSelectors.forEach(selector => {
                const el = document.querySelector(selector);
                if (el) el.value = '';
            });
            load(1);
        });

        return { load };
    }

    // Generic styled confirmation modal - replaces raw browser confirm()
    // everywhere so every "are you sure?" in the app looks the same.
    // Usage: confirmAction('Delete this item?', function () { ...do it... });
    // Optional third arg: { title, warning, confirmText, danger: true|false }
    function confirmAction(message, onConfirm, options = {}) {
        const modalEl = document.getElementById('genericConfirmModal');
        if (!modalEl) {
            // Fallback so nothing silently breaks if the modal markup is ever missing.
            if (confirm(message)) onConfirm();
            return;
        }

        const danger = options.danger !== false;
        modalEl.querySelector('.modal-header').className = 'modal-header py-1 px-2 ' + (danger ? 'bg-danger-subtle' : 'bg-warning-subtle');
        modalEl.querySelector('.modal-title i').className = 'ti ti-alert-triangle me-1 ' + (danger ? 'text-danger' : 'text-warning');
        modalEl.querySelector('.modal-title-text').textContent = options.title || 'Confirm Action';
        modalEl.querySelector('.modal-body-message').textContent = message;

        const warningEl = modalEl.querySelector('.modal-body-warning');
        if (options.warning) {
            warningEl.textContent = options.warning;
            warningEl.classList.remove('d-none');
        } else {
            warningEl.classList.add('d-none');
        }

        const confirmBtn = modalEl.querySelector('.modal-confirm-btn');
        // Keep the 'modal-confirm-btn' marker class - a plain assignment
        // here drops it, so any confirmAction() call after the first one
        // on the same page can no longer find this button at all.
        confirmBtn.className = 'btn btn-sm rounded-sm modal-confirm-btn ' + (danger ? 'btn-danger' : 'btn-warning');
        confirmBtn.innerHTML = '<i class="ti ti-check me-1"></i> ' + (options.confirmText || (danger ? 'Delete' : 'Confirm'));

        // Replace the button node so old click listeners from a previous
        // confirmAction() call on this same page don't also fire.
        const freshBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(freshBtn, confirmBtn);
        freshBtn.addEventListener('click', function () {
            bootstrap.Modal.getInstance(modalEl)?.hide();
            onConfirm();
        });

        new bootstrap.Modal(modalEl).show();
    }

    // Convenience wrapper: confirm, then submit the button's own form.
    // <button type="button" onclick="confirmSubmit(this, 'Delete this?')">
    function confirmSubmit(btn, message, options = {}) {
        confirmAction(message, function () { btn.closest('form').submit(); }, options);
    }

    // Convenience wrapper: confirm, then follow the link's href.
    // <a href="..." onclick="confirmNavigate(this, 'Approve this?'); return false;">
    function confirmNavigate(link, message, options = {}) {
        confirmAction(message, function () { window.location.href = link.href; }, options);
    }
    </script>

    <!-- Generic confirmation modal, driven by confirmAction() above -->
    <div class="modal fade" id="genericConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-sm">
                <div class="modal-header py-1 px-2 bg-danger-subtle">
                    <h5 class="modal-title" style="font-size: 0.8rem;">
                        <i class="ti ti-alert-triangle text-danger me-1"></i> <span class="modal-title-text">Confirm Action</span>
                    </h5>
                    <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-2 px-2">
                    <p class="modal-body-message" style="font-size: 0.75rem;"></p>
                    <div class="modal-body-warning alert alert-warning py-1 px-2 mb-0 rounded-sm d-none" style="font-size: 0.65rem;"></div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">
                        <i class="ti ti-x me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm rounded-sm modal-confirm-btn" style="font-size: 0.7rem;">
                        <i class="ti ti-check me-1"></i> Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>