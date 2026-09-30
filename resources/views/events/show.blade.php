<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app_abbreviation() }} | {{ $event->title }}</title>

    <link rel="stylesheet" href="{{ asset('tabler/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendor/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap">

    <style>
        :root {
            --ink: #1B1926;
            --ink-soft: #5C5A6E;
            --paper: #FAF9FC;
            --paper-raised: #FFFFFF;
            --line: #E3E0EC;
            --navy: #1B2F6B;
            --navy-deep: #0F1C45;
            --navy-soft: #E7EAF6;
            --gold: #C9A227;
            --gold-deep: #96771A;
            --gold-soft: #FBF3DD;
        }
        * { font-family: 'Public Sans', sans-serif; }
        body { background: var(--paper); color: var(--ink); }
        .btn { border-radius: 6px; }

        .event-header { background: var(--navy); color: #fff; padding: 2.5rem 0 3rem; }
        .event-header a.back-link { color: rgba(255,255,255,0.75); font-size: 0.85rem; text-decoration: none; }
        .event-header a.back-link:hover { color: #fff; }
        .event-eyebrow { color: var(--gold); font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; }
        .event-title { font-weight: 600; font-size: clamp(1.6rem, 3.5vw, 2.4rem); margin-top: 0.5rem; }

        .event-meta-card {
            background: var(--paper-raised); border: 1px solid var(--line); border-radius: 10px;
            padding: 1.5rem; margin-top: -3rem; position: relative; z-index: 2;
        }
        .event-meta-item { display: flex; align-items: flex-start; gap: 0.6rem; font-size: 0.85rem; }
        .event-meta-item i { color: var(--gold-deep); font-size: 1.1rem; }
        .event-body { font-size: 0.95rem; line-height: 1.7; color: var(--ink); }
        .footer-section { background: var(--navy-deep); color: rgba(255,255,255,0.6); font-size: 0.8rem; }
    </style>
</head>
<body>

    <div class="event-header">
        <div class="container">
            <a href="{{ route('landing') }}" class="back-link"><i class="ti ti-arrow-left me-1"></i> Back to Staff MS</a>
            <div class="event-eyebrow mt-4">Event</div>
            <h1 class="event-title">{{ $event->title }}</h1>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="event-meta-card">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="event-meta-item">
                                <i class="ti ti-calendar"></i>
                                <div>
                                    <div class="fw-semibold">{{ $event->event_date->format('l, d F Y') }}</div>
                                    @if($event->start_time)
                                        <div class="text-muted" style="font-size: 0.78rem;">{{ $event->start_time }}@if($event->end_time) - {{ $event->end_time }}@endif</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($event->location)
                        <div class="col-sm-4">
                            <div class="event-meta-item">
                                <i class="ti ti-map-pin"></i>
                                <div class="fw-semibold">{{ $event->location }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="py-5">
                    <p class="event-body fw-semibold">{{ $event->description }}</p>
                    @if($event->body)
                        <div class="event-body mt-3" style="white-space: pre-line;">{{ $event->body }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <footer class="py-4 footer-section text-center">
        &copy; {{ date('Y') }} Staff Management System. All rights reserved.
    </footer>

</body>
</html>
