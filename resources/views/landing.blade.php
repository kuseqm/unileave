<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app_abbreviation() }} | University Staff & Leave Management</title>

    <!-- Tabler CSS (Local - Already installed) -->
    <link rel="stylesheet" href="{{ asset('tabler/tabler.min.css') }}">

    <!-- Tabler Icons -->
    <link rel="stylesheet" href="{{ asset('css/vendor/tabler-icons.min.css') }}">

    <!-- Public Sans, one weight-driven typeface throughout - bold/black
         weights carry the headlines instead of a separate display serif,
         closer to how MMG uses one confident sans-serif at every size. -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap">

    <style>
        /* ============================================
           COLOR SYSTEM - drawn directly from the SJUT crest:
           deep royal navy (the shield), a vivid purple (the
           mitre and border), and gold (the laurels and "SJUT"
           lettering itself) - not invented, not generic blue/
           orange SaaS colors.
           ============================================ */
        :root {
            --ink: #1B1926;
            --ink-soft: #5C5A6E;
            --paper: #FAF9FC;
            --paper-raised: #FFFFFF;
            --line: #E3E0EC;

            --navy: #1B2F6B;
            --navy-deep: #0F1C45;
            --navy-soft: #E7EAF6;

            --purple: #8E2FB8;
            --purple-deep: #641F87;
            --purple-soft: #F3E7F9;

            --gold: #C9A227;
            --gold-deep: #96771A;
            --gold-soft: #FBF3DD;
        }

        * { font-family: 'Public Sans', sans-serif; }
        h1, h2, h3, h4, .display-hero { font-weight: 800; letter-spacing: -0.01em; }

        body { background: var(--paper); color: var(--ink); }

        .py-6 { padding-top: 4rem; padding-bottom: 4rem; }

        /* --- Buttons: sharp-ish corners throughout, not pill-shaped -
           closer to how MMG's own buttons read (4-6px, not 50px). --- */
        .btn { border-radius: 6px; }
        .btn-navy {
            background: var(--navy); border-color: var(--navy); color: #fff;
        }
        .btn-navy:hover { background: var(--navy-deep); border-color: var(--navy-deep); color: #fff; }
        .btn-outline-navy {
            border: 1px solid var(--navy); color: var(--navy); background: transparent;
        }
        .btn-outline-navy:hover { background: var(--navy); color: #fff; }
        .btn-gold {
            background: var(--gold); border-color: var(--gold); color: var(--navy-deep); font-weight: 500;
        }
        .btn-gold:hover { background: var(--gold-deep); border-color: var(--gold-deep); color: #fff; }
        .btn-verify-nav {
            border-radius: 6px; padding: 0.4rem 1rem; margin-left: 0.5rem; transition: all 0.3s;
        }

        /* --- Navbar: transparent and floating over the hero photo, like
           MMG's own header - turns solid only once the page is scrolled
           past the hero (see the scroll listener near the footer). --- */
        .site-nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1030;
            background: transparent; border-bottom: 1px solid transparent;
            transition: background-color 0.25s ease, border-color 0.25s ease;
        }
        .site-nav .nav-link, .site-nav .navbar-brand-mark { color: #fff; }
        .site-nav .btn-outline-navy { border-color: rgba(255,255,255,0.6); color: #fff; }
        .site-nav .btn-outline-navy:hover { background: #fff; color: var(--navy); }
        .site-nav.is-scrolled {
            background: rgba(255,255,255,0.96); border-bottom-color: var(--line);
            backdrop-filter: blur(6px);
        }
        .site-nav.is-scrolled .nav-link, .site-nav.is-scrolled .navbar-brand-mark { color: var(--ink); }
        .site-nav.is-scrolled .navbar-brand-mark .accent { color: var(--gold-deep); }
        .site-nav.is-scrolled .btn-outline-navy { border-color: var(--navy); color: var(--navy); }
        .site-nav.is-scrolled .btn-outline-navy:hover { background: var(--navy); color: #fff; }
        .navbar-brand-mark { font-weight: 900; }
        .navbar-brand-mark .accent { color: var(--gold); }
        .site-nav .navbar-toggler { border-color: rgba(255,255,255,0.6); }
        .site-nav .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        .site-nav.is-scrolled .navbar-toggler { border-color: var(--line); }
        .site-nav.is-scrolled .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(27,25,38,0.75)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        section[id] { scroll-margin-top: 88px; }

        @media (max-width: 991.98px) {
            .site-nav .navbar-collapse.show {
                background: rgba(255,255,255,0.98); border-radius: 8px; padding: 1rem; margin-top: 0.5rem;
                box-shadow: 0 10px 30px rgba(15,28,69,0.15);
            }
            .site-nav .navbar-collapse.show .nav-link, .site-nav .navbar-collapse.show .navbar-brand-mark { color: var(--ink); }
            .site-nav .navbar-collapse.show .btn-outline-navy { border-color: var(--navy); color: var(--navy); }
        }

        /* --- Hero: full-bleed photo, minimal chrome, text sits directly
           on the image with a left-side scrim rather than a full wash -
           the photo itself should stay visible, per MMG's own hero. --- */
        .hero-section {
            background: linear-gradient(160deg, var(--navy) 0%, var(--navy-deep) 100%);
            position: relative;
            overflow: hidden;
            min-height: 85vh;
            display: flex;
            align-items: center;
            padding-top: 6rem;
            padding-bottom: 4rem;
            border-bottom: 4px solid var(--gold);
        }
        /* Reserves room so the hero text/CTA never collides with the
           announcements strip overlaid at the bottom of the photo. */
        .hero-has-strip { padding-bottom: clamp(14rem, 28vh, 18rem); }
        .hero-bg-slides { position: absolute; inset: 0; z-index: 0; }
        .hero-bg-slide {
            position: absolute; inset: 0;
            background-size: cover; background-position: center;
            opacity: 0; transition: opacity 1.8s ease-in-out;
        }
        .hero-bg-slide.is-active { opacity: 1; }
        .hero-has-photo::before {
            content: '';
            position: absolute; inset: 0; z-index: 1;
            background: linear-gradient(90deg, rgba(10,20,50,0.78) 0%, rgba(10,20,50,0.55) 30%, rgba(10,20,50,0.18) 60%, rgba(10,20,50,0.05) 100%);
        }
        .hero-section > .container { position: relative; z-index: 2; }
        .hero-section > .hero-strip { z-index: 3; }
        .hero-motif {
            position: absolute; top: 0; right: 0; height: 100%; width: 55%;
            opacity: 0.9; pointer-events: none;
        }
        .hero-kicker {
            display: inline-flex; align-items: center; gap: 0.4rem;
            font-size: 0.72rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase;
            color: var(--gold); border: 1px solid rgba(201,162,39,0.45); border-radius: 6px;
            padding: 0.3rem 0.9rem; margin-bottom: 1.5rem;
        }
        .display-hero {
            font-size: clamp(1.9rem, 3.4vw, 2.75rem);
            font-weight: 600;
            line-height: 1.15;
            letter-spacing: -0.01em;
            color: #fff;
        }
        .display-hero .accent { color: var(--gold); }
        .hero-lede { color: rgba(255,255,255,0.85); font-size: 1.05rem; font-weight: 300; max-width: 42ch; }

        /* --- Stats strip (single source now - was duplicated before) --- */
        .stats-strip { background: var(--paper-raised); border-bottom: 1px solid var(--line); }
        .stat-figure { font-size: 2.1rem; font-weight: 800; color: var(--navy); }
        .stat-label { font-size: 0.75rem; color: var(--ink-soft); }

        /* --- Section framing --- */
        .section-eyebrow {
            font-size: 0.72rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--gold-deep);
        }
        .bg-parchment { background: var(--paper); }
        .bg-navy-soft { background: var(--navy-soft); }

        /* --- Features: full-bleed navy band, borderless items - a thin
           top rule instead of a boxed card + shadow. --- */
        .features-section { background: var(--navy); }
        .feature-item { border-top: 2px solid rgba(255,255,255,0.18); padding-top: 1.25rem; height: 100%; }
        .feature-item-icon {
            display: block; font-size: 1.6rem; color: var(--gold); margin-bottom: 0.9rem;
        }
        .feature-item h4 { font-size: 1.05rem; font-weight: 600; color: #fff; }
        .feature-item p { font-size: 0.85rem; color: rgba(255,255,255,0.72); margin-bottom: 0; }

        /* --- Leave type badges --- */
        .leave-badge {
            display: inline-block; background: var(--gold-soft); color: var(--gold-deep);
            border: 1px solid rgba(201,162,39,0.4);
            border-radius: 50px; padding: 0.45rem 1rem; margin: 0.25rem; font-size: 0.8rem; font-weight: 500;
        }

        /* --- Announcements: fixed 4-across card grid overlaid on the
           bottom edge of the hero photo, matching MMG's own story-strip
           treatment (dark semi-transparent cards, not a separate colored
           section) - always shows as a row, never scrolls. --- */
        .hero-strip {
            position: absolute; left: 0; right: 0; bottom: 0;
            padding-bottom: clamp(1.25rem, 3vh, 2rem);
            z-index: 2;
        }
        .announcement-track {
            display: flex; gap: 0.75rem;
        }
        .announcement-slide {
            flex: 1 1 0;
            min-width: 0;
            background: rgba(10,20,50,0.48); backdrop-filter: blur(4px);
            border-top: 3px solid rgba(255,255,255,0.25);
            border-radius: 4px; padding: 1.1rem 1rem;
        }
        .announcement-slide.pinned { border-top-color: var(--gold); }
        @media (max-width: 767.98px) {
            .announcement-track { flex-wrap: wrap; }
            .announcement-slide { flex: 1 1 calc(50% - 0.375rem); }
        }
        .announcement-cat {
            font-size: 0.6rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: var(--gold);
        }
        .announcement-slide h5 { color: #fff; font-size: 0.88rem; }
        .announcement-slide p { color: rgba(255,255,255,0.75); }
        .announcement-date { font-size: 0.65rem; color: rgba(255,255,255,0.6); }

        /* --- Governance: a photo card, copy overlaid directly on it (same
           treatment as the hero), sitting side by side with the calendar
           instead of its own full-width band. --- */
        .governance-hero {
            position: relative; overflow: hidden;
            min-height: 24rem; height: 100%; display: flex; align-items: flex-end;
        }
        .governance-hero--flush { border-radius: 0; }
        .governance-hero-media {
            position: absolute; inset: 0;
            background-size: cover; background-position: center;
        }
        .governance-hero-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(0deg, rgba(10,20,50,0.9) 0%, rgba(10,20,50,0.5) 55%, rgba(10,20,50,0.15) 100%);
        }
        .governance-hero-copy { padding: clamp(1.5rem, 4vw, 3rem); }
        .governance-hero-copy p { max-width: 42ch; }

        /* --- Calendar panel: sits flush against the viewport edge like the
           governance photo beside it - only its own content gets padding,
           the panel itself has none. --- */
        .calendar-panel {
            height: 100%; padding: clamp(1.5rem, 4vw, 3rem);
            display: flex; flex-direction: column; justify-content: center;
        }
        .calendar-widget { width: 100%; }
        .calendar-grid {
            display: grid; grid-template-columns: repeat(7, 1fr);
            gap: 1px; background: var(--line); border: 1px solid var(--line); border-radius: 8px; overflow: hidden;
        }
        .calendar-weekday {
            background: var(--navy-soft); color: var(--navy);
            font-size: 0.65rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;
            text-align: center; padding: 0.5rem 0.25rem;
        }
        .calendar-day {
            background: var(--paper-raised); min-height: 4.5rem;
            padding: 0.35rem; display: flex; flex-direction: column; gap: 0.15rem;
        }
        .calendar-day.is-muted { background: var(--paper); }
        .calendar-day.is-muted .calendar-day-number { color: var(--ink-soft); opacity: 0.5; }
        .calendar-day.is-today { background: var(--gold-soft); }
        .calendar-day-number { font-size: 0.75rem; font-weight: 600; color: var(--ink); }
        .calendar-event-link {
            display: block; background: var(--navy); color: #fff;
            font-size: 0.62rem; font-weight: 500; line-height: 1.3;
            padding: 0.1rem 0.3rem; border-radius: 3px; text-decoration: none;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .calendar-event-link:hover { background: var(--gold); color: var(--navy-deep); }
        @media (max-width: 575.98px) {
            .calendar-day { min-height: 3.2rem; font-size: 0.7rem; }
            .calendar-event-link { font-size: 0.55rem; }
        }

        /* --- FAQ --- */
        .accordion-button:not(.collapsed) {
            background: var(--navy-soft); color: var(--navy-deep); box-shadow: none;
        }
        .accordion-button:focus { box-shadow: none; border-color: var(--line); }

        /* --- Team ("Built by") --- */
        .team-section { background: var(--navy); }
        .team-photo-frame {
            width: 128px; height: 128px; margin: 0 auto;
            border-radius: 50%; overflow: hidden;
            background: var(--navy-soft); border: 3px solid var(--gold);
            display: flex; align-items: center; justify-content: center;
        }
        .team-photo-frame img {
            width: 100%; height: 100%; object-fit: cover; object-position: center;
        }
        .team-photo-frame span {
            font-size: 2.2rem; font-weight: 700; color: var(--navy);
        }

        /* --- Footer --- */
        .footer-section { background: var(--navy-deep); color: rgba(255,255,255,0.65); }
        .footer-brand { font-weight: 900; color: #fff; }
    </style>
</head>
<body>

    <!-- Navbar - transparent over the hero photo, solidifies on scroll -->
    <nav class="navbar navbar-expand-lg site-nav" id="siteNav">
        <div class="container">
            <a class="navbar-brand navbar-brand-mark fs-2" href="/">
                Staff<span class="accent">MS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#leave-types">Leave Types</a></li>
                    @if($announcements->isNotEmpty())
                    <li class="nav-item"><a class="nav-link" href="#announcements">Announcements</a></li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                    <li class="nav-item">
                        <a class="btn btn-outline-navy btn-verify-nav ms-2" href="{{ route('verify.index') }}">
                            <i class="ti ti-qrcode me-1"></i> Verify Document
                        </a>
                    </li>
                </ul>
                <div class="ms-3">
                    <a href="{{ route('login') }}" class="btn btn-outline-navy me-2">Sign In</a>
                    <a href="{{ route('register') }}" class="btn btn-navy">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    @php
        // Drop a real photo at public/images/landing/hero/campus.jpg and the
        // hero switches to it automatically - see the README in that folder.
        // Extra photos named campus-2.jpg, campus-3.jpg, campus-4.jpg (etc.)
        // turn the hero into a slow auto-rotating background - drop just
        // campus.jpg for a single static photo, or add more for rotation.
        // Until any photo exists, an abstract pattern (built from the
        // crest's own laurel and shield motifs, not a stock photo) fills
        // the same role.
        $heroSlideNames = ['campus.jpg', 'campus-2.jpg', 'campus-3.jpg', 'campus-4.jpg', 'campus-5.jpg', 'campus-6.jpg'];
        $heroSlides = array_values(array_filter($heroSlideNames, fn ($name) => file_exists(public_path('images/landing/hero/'.$name))));
        $hasHeroPhoto = count($heroSlides) > 0;
    @endphp

    <!-- Hero Section -->
    <section class="hero-section {{ $hasHeroPhoto ? 'hero-has-photo' : '' }} {{ $announcements->isNotEmpty() ? 'hero-has-strip' : '' }}">
        @if($hasHeroPhoto)
        <div class="hero-bg-slides">
            @foreach($heroSlides as $index => $slide)
            <div class="hero-bg-slide {{ $index === 0 ? 'is-active' : '' }}" style="background-image: url('{{ asset('images/landing/hero/'.$slide) }}');"></div>
            @endforeach
        </div>
        @endif
        @unless($hasHeroPhoto)
        <svg class="hero-motif" viewBox="0 0 900 700" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
            <circle cx="720" cy="180" r="260" fill="none" stroke="#fff" stroke-opacity="0.10" stroke-width="1.5" />
            <circle cx="720" cy="180" r="190" fill="none" stroke="#fff" stroke-opacity="0.14" stroke-width="1.5" />
            <circle cx="720" cy="180" r="120" fill="var(--gold)" fill-opacity="0.10" />
            <g fill="var(--gold)" fill-opacity="0.22">
                <ellipse cx="120" cy="600" rx="14" ry="34" />
                <ellipse cx="150" cy="560" rx="14" ry="34" transform="rotate(-18 150 560)" />
                <ellipse cx="185" cy="525" rx="14" ry="34" transform="rotate(-34 185 525)" />
                <ellipse cx="80" cy="555" rx="14" ry="34" transform="rotate(18 80 555)" />
                <ellipse cx="60" cy="510" rx="14" ry="34" transform="rotate(34 60 510)" />
            </g>
        </svg>
        @endunless
        <div class="container position-relative">
            <div class="row">
                <div class="col-lg-6">
                    <span class="hero-kicker"><i class="ti ti-building-bank"></i> Built for university administration</span>
                    <h1 class="display-hero">Every leave request,<br>tracked <span class="accent">start to finish</span>.</h1>
                    <p class="hero-lede mt-3">Staff records, ID cards, leave requests, and multi-level approvals - for thousands of staff across every campus, in one place.</p>
                    <div class="mt-4">
                        <a href="{{ route('register') }}" class="btn btn-gold">Get Started</a>
                        <a href="#features" class="ms-3" style="color: #fff; font-size: 0.9rem; border-bottom: 1px solid rgba(255,255,255,0.5); padding-bottom: 2px;">Learn more →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Announcements strip - overlaid on the bottom edge of the hero
             photo, matching MMG's own dark card-strip treatment, instead
             of a separate section below the fold. Always a fixed 4-across
             row (routes/web.php caps the query at 4) - no scrolling. -->
        @if($announcements->isNotEmpty())
        <div class="hero-strip">
            <div class="container">
                <div class="announcement-track">
                    @foreach($announcements as $a)
                    <div class="announcement-slide {{ $a->is_pinned ? 'pinned' : '' }}">
                        <span class="announcement-cat">
                            @if($a->is_pinned)<i class="ti ti-pin"></i>@endif
                            {{ ucfirst($a->category) }}
                        </span>
                        <h5 class="mt-2 mb-1">{{ $a->title }}</h5>
                        <p class="mb-2" style="font-size: 0.78rem;">{{ \Illuminate\Support\Str::limit($a->body, 70) }}</p>
                        <span class="announcement-date">{{ $a->created_at->format('d M Y') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </section>

    <!-- Governance Section - a single full-bleed photo with the copy
         overlaid directly on it (same treatment as the hero), followed by
         a real month calendar so upcoming events have a natural home next
         to the governance story. -->
    <section style="background: var(--paper-raised);">
        <div class="row g-0 align-items-stretch mx-0">
            <div class="col-lg-6 px-0">
                <div class="governance-hero governance-hero--flush">
                    <div class="governance-hero-media" style="background-image: url('{{ asset('images/landing/governance/session-1.jpg') }}');" role="img" aria-label="University staff reviewing documents in a governance meeting"></div>
                    <div class="governance-hero-overlay"></div>
                    <div class="governance-hero-copy position-relative">
                        <div class="section-eyebrow" style="color: var(--gold);">Governance</div>
                        <h2 class="fw-semibold mt-1" style="color: #fff;">Decisions made in the room, tracked in the system</h2>
                        <p class="mt-3" style="color: rgba(255,255,255,0.82);">Departmental and Senate meetings review every case in session - the same approval that starts at that table flows straight into the workflow staff see on their dashboard. Every attendee's vote and comment is recorded, case by case.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 px-0">
                <div class="calendar-panel">
                    <div class="section-eyebrow">Calendar</div>
                    <h2 class="fw-semibold mt-1">Upcoming on Campus</h2>
                    <div class="calendar-widget mt-3" id="calendarWidget">
                        @include('partials.calendar-inner')
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section - full-bleed navy band, borderless items (a thin
         top rule instead of a boxed card + shadow), matching the hero's
         confidence instead of a conventional SaaS card grid. -->
    <section id="features" class="py-6 features-section text-white">
        <div class="container">
            <div class="mb-5" style="max-width: 46ch;">
                <div class="section-eyebrow" style="color: var(--gold);">Why StaffMS</div>
                <h2 class="fw-semibold mt-1">A system built for how universities actually work</h2>
                <p class="mt-2" style="color: rgba(255,255,255,0.75);">A comprehensive solution designed specifically for university administration.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-item">
                        <i class="ti ti-cloud-upload feature-item-icon"></i>
                        <h4>Online Applications</h4>
                        <p>Apply for leave anytime, anywhere. Upload required documents securely.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item">
                        <i class="ti ti-chart-line feature-item-icon"></i>
                        <h4>Real-time Tracking</h4>
                        <p>See exactly where your request is in the approval workflow.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item">
                        <i class="ti ti-bell feature-item-icon"></i>
                        <h4>Instant Notifications</h4>
                        <p>Get email and in-app notifications for every status change.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item">
                        <i class="ti ti-file-description feature-item-icon"></i>
                        <h4>Comprehensive Reports</h4>
                        <p>Generate Excel, CSV, and PDF reports.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item">
                        <i class="ti ti-device-mobile feature-item-icon"></i>
                        <h4>Mobile Ready</h4>
                        <p>Install as an app on your Android phone.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item">
                        <i class="ti ti-shield feature-item-icon"></i>
                        <h4>Secure &amp; Compliant</h4>
                        <p>Compliant with Tanzanian labour laws.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Leave Types + FAQ - side by side, so the page isn't just one
         section stacked under another for two related "reference" blocks. -->
    <section id="leave-types" class="py-6" style="background: var(--paper-raised);">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="section-eyebrow">Coverage</div>
                    <h2 class="fw-semibold mt-1">Leave Types Supported</h2>
                    <p class="text-muted mt-2">11 different leave types with configurable document requirements</p>
                    <div class="mt-4">
                        <span class="leave-badge">LC1 - Annual Leave</span>
                        <span class="leave-badge">LC2 - Sick Leave</span>
                        <span class="leave-badge">LC3 - Compassionate</span>
                        <span class="leave-badge">LC4 - Maternity/Paternity</span>
                        <span class="leave-badge">LC5 - Special Leave</span>
                        <span class="leave-badge">LC6 - Study Leave</span>
                        <span class="leave-badge">LC7 - Sabbatical</span>
                        <span class="leave-badge">LC8 - Unpaid Leave</span>
                        <span class="leave-badge">LC9 - Care's Leave</span>
                        <span class="leave-badge">LC10 - University Business</span>
                        <span class="leave-badge">LC11 - Custom Types</span>
                    </div>
                </div>
                <div class="col-lg-6" id="faq">
                    <div class="section-eyebrow">Questions</div>
                    <h2 class="fw-semibold mt-1">Frequently Asked Questions</h2>
                    <div class="accordion mt-3" id="faqAccordion">
                        <div class="accordion-item" style="border-color: var(--line);">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Who can use Staff Management System?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted" style="font-size: 0.85rem;">
                                    Any staff member at a participating university - academic and administrative - can apply for leave, track requests, and manage their profile. Approvers (HODs, Deans, DVCs, HR) use the same login with their own review dashboard.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" style="border-color: var(--line);">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    How long does approval usually take?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted" style="font-size: 0.85rem;">
                                    It depends on the leave type and how many stages it passes through. You can track exactly where your request is - and who it's waiting on - in real time from your dashboard.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" style="border-color: var(--line);">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Can I verify a leave letter I received?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted" style="font-size: 0.85rem;">
                                    Yes - every official letter carries a QR code and secure code. Use "Verify Document" in the navigation bar above to confirm a letter is genuine, without needing to log in.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" style="border-color: var(--line);">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Is my data secure?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted" style="font-size: 0.85rem;">
                                    Yes. Access is role-based - you only ever see what's relevant to you or your approval duties - and every action is logged for audit purposes, in line with Tanzanian labour law requirements.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Visitor count - real, counted once per browser session in the
         landing route (routes/web.php) - a trust signal right before the
         signup ask, instead of a row of made-up numbers. -->
    <section class="stats-strip py-5">
        <div class="container text-center">
            <div class="stat-figure" style="font-size: 3rem;">{{ number_format($visitorCount) }}</div>
            <div class="stat-label">Visitors and counting</div>
        </div>
    </section>

    <!-- Team Members ("Built by") - replaces the generic CTA banner with
         the people who actually built the system. Hidden entirely until
         at least one member is added via /admin/team-members. -->
    @if($teamMembers->isNotEmpty())
    <section class="py-6 team-section">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-eyebrow" style="color: var(--gold);">Built By</div>
                <h2 class="fw-semibold mt-1" style="color: #fff;">The team behind StaffMS</h2>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach($teamMembers as $member)
                <div class="col-6 col-md-4 col-lg-3 text-center">
                    <div class="team-photo-frame">
                        @if($member->photo_url)
                            <img src="{{ $member->photo_url }}" alt="{{ $member->name }}">
                        @else
                            <span>{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <h5 class="mt-3 mb-0" style="color: #fff; font-size: 0.95rem;">{{ $member->name }}</h5>
                    <div style="color: var(--gold); font-size: 0.75rem;">{{ $member->role }}</div>
                    @if($member->bio)
                        <p class="mt-2" style="color: rgba(255,255,255,0.7); font-size: 0.78rem;">{{ $member->bio }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer class="py-5 footer-section">
        <div class="container text-center">
            <div class="footer-brand fs-4 mb-2">Staff<span style="color: var(--gold);">MS</span></div>
            <p class="mb-0" style="font-size: 0.8rem;">&copy; {{ date('Y') }} Staff Management System. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="{{ asset('js/vendor/bootstrap.bundle.min.js') }}"></script>

    <!-- Tabler JS (Local) -->
    <script src="{{ asset('tabler/tabler.min.js') }}"></script>

    <script>
        (function () {
            var nav = document.getElementById('siteNav');
            if (!nav) return;

            function updateNav() {
                nav.classList.toggle('is-scrolled', window.scrollY > 40);
            }

            updateNav();
            window.addEventListener('scroll', updateNav, { passive: true });
        })();
    </script>

    <script>
        (function () {
            var slides = document.querySelectorAll('.hero-bg-slide');
            if (slides.length < 2) return;
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

            var current = 0;
            setInterval(function () {
                slides[current].classList.remove('is-active');
                current = (current + 1) % slides.length;
                slides[current].classList.add('is-active');
            }, 5000);
        })();
    </script>

    <script>
        (function () {
            var widget = document.getElementById('calendarWidget');
            if (! widget) return;

            widget.addEventListener('click', function (e) {
                var link = e.target.closest('.calendar-nav-link');
                if (! link) return;

                e.preventDefault();

                fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (response) { return response.text(); })
                    .then(function (html) { widget.innerHTML = html; })
                    .catch(function () { window.location.href = link.href; });
            });
        })();
    </script>
</body>
</html>
