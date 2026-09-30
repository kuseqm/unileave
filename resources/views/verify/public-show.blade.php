<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ app_abbreviation() }} | Document Verification</title>
    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('css/vendor/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="{{ asset('css/vendor/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap">
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
        }
        * { font-family: 'Public Sans', sans-serif; }
        body {
            background: var(--paper);
            color: var(--ink);
            padding-bottom: 40px;
        }

        /* --- Photo header, same rotating-slide treatment as the landing
           page hero. --- */
        .verify-hero {
            position: relative; overflow: hidden;
            min-height: 15rem; display: flex; flex-direction: column;
        }
        .verify-hero-slides { position: absolute; inset: 0; z-index: 0; }
        .verify-hero-slide {
            position: absolute; inset: 0;
            background-size: cover; background-position: center;
            opacity: 0; transition: opacity 1.8s ease-in-out;
        }
        .verify-hero-slide.is-active { opacity: 1; }
        .verify-hero-overlay {
            position: absolute; inset: 0; z-index: 1;
            background: linear-gradient(160deg, rgba(15,28,69,0.92) 0%, rgba(15,28,69,0.72) 100%);
        }
        .verify-hero-topbar {
            position: relative; z-index: 2;
            display: flex; justify-content: space-between; align-items: center;
            padding: 1rem 1.5rem;
        }
        .verify-hero-topbar a.brand { font-weight: 900; font-size: 1.3rem; color: #fff; text-decoration: none; }
        .verify-hero-topbar a.brand .accent { color: var(--gold); }
        .verify-hero-topbar a.back-link { color: rgba(255,255,255,0.8); font-size: 0.85rem; text-decoration: none; }
        .verify-hero-topbar a.back-link:hover { color: #fff; }
        .verify-hero-copy {
            position: relative; z-index: 2;
            flex: 1; display: flex; flex-direction: column; justify-content: center;
            text-align: center; padding: 1rem 1.5rem 3rem;
        }
        .verify-hero-copy h2 { font-weight: 800; color: #fff; margin: 0; }
        .verify-hero-copy p { color: rgba(255,255,255,0.8); font-size: 0.9rem; margin: 0.35rem 0 0; }

        .verification-container {
            max-width: 800px;
            margin: -2.5rem auto 0;
            padding: 0 20px;
            position: relative; z-index: 3;
        }
        .card-verify {
            border: 1px solid var(--line);
            border-radius: 10px;
            box-shadow: 0 20px 40px -15px rgba(15,28,69,0.25);
            background: var(--paper-raised);
        }
        .card-header {
            background-color: var(--navy-soft);
            border-bottom: 1px solid var(--line);
            padding: 0.75rem 1rem;
            font-weight: 700;
            color: var(--navy);
        }
        .btn { border-radius: 6px; }
        .btn-primary {
            background-color: var(--navy);
            border-color: var(--navy);
        }
        .btn-primary:hover {
            background-color: var(--navy-deep);
            border-color: var(--navy-deep);
        }
        .btn-outline-secondary {
            border-color: var(--line); color: var(--ink-soft);
        }
        .btn-outline-secondary:hover {
            background-color: var(--navy); border-color: var(--navy); color: #fff;
        }
    </style>
</head>
<body>
    <div class="verify-hero">
        <div class="verify-hero-slides">
            @foreach(['campus.jpg', 'campus-2.jpg', 'campus-3.jpg', 'campus-4.jpg'] as $index => $slide)
                @if(file_exists(public_path('images/landing/hero/'.$slide)))
                    <div class="verify-hero-slide {{ $index === 0 ? 'is-active' : '' }}" style="background-image: url('{{ asset('images/landing/hero/'.$slide) }}');"></div>
                @endif
            @endforeach
        </div>
        <div class="verify-hero-overlay"></div>
        <div class="verify-hero-topbar">
            <a href="{{ route('landing') }}" class="brand">Staff<span class="accent">MS</span></a>
            <a href="{{ route('landing') }}" class="back-link"><i class="ti ti-arrow-left me-1"></i> Back to home</a>
        </div>
        <div class="verify-hero-copy">
            <h2>Staff Management System</h2>
            <p>Official Document Verification Portal</p>
        </div>
    </div>

    <div class="verification-container">
        <div class="card card-verify">
            <div class="card-header">
                <i class="ti ti-check-circle"></i> Verification Result
            </div>
            <div class="card-body p-4">
                <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
                    <i class="ti ti-check"></i>
                    <div>This document is genuine and was issued by the Staff Management System system.</div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Verification Code:</strong> <span class="badge p-2" style="background: var(--navy); color: #fff;">{{ $log->verification_code }}</span></p>
                        <p><strong>Report Type:</strong> {{ $log->reportDefinition->display_name ?? 'N/A' }}</p>
                        <p><strong>Generated By:</strong> {{ $log->user->full_name ?? 'System' }}</p>
                        <p><strong>Generated On:</strong> {{ $log->created_at->format('d M Y H:i:s') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Filters Applied:</strong></p>
                        <pre class="bg-light p-2 rounded" style="font-size: 12px;">{{ json_encode($log->filters_applied, JSON_PRETTY_PRINT) }}</pre>
                        <p><strong>Verified At:</strong> {{ $log->verified_at ? $log->verified_at->format('d M Y H:i:s') : 'First verification' }}</p>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('verify.print', $log->verification_code) }}" class="btn btn-primary px-4">Print Certificate</a>
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary ms-2">Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var slides = document.querySelectorAll('.verify-hero-slide');
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
</body>
</html>
