<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ app_abbreviation() }} | Verification Failed</title>
    <link href="{{ asset('css/vendor/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/vendor/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap">
    <style>
        body {
            background: #FAF9FC;
            font-family: 'Public Sans', sans-serif;
            margin: 0;
            min-height: 100vh;
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
        .verify-hero-topbar a.brand .accent { color: #C9A227; }
        .verify-hero-topbar a.back-link { color: rgba(255,255,255,0.8); font-size: 0.85rem; text-decoration: none; }
        .verify-hero-topbar a.back-link:hover { color: #fff; }

        .error-card-wrap {
            max-width: 550px; margin: -2.5rem auto 40px; padding: 0 20px;
            position: relative; z-index: 3;
        }
        .error-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 20px 40px -15px rgba(15,28,69,0.25);
        }
        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1.2rem;
            background: #fff5f5;
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-left: auto;
            margin-right: auto;
        }
        h3 {
            color: #1B2F6B;
            margin-bottom: 1rem;
            font-weight: 800;
            font-size: 1.8rem;
        }
        .code-highlight {
            background: #f8f9fa;
            padding: 0.2rem 0.8rem;
            border-radius: 30px;
            font-family: monospace;
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 1px;
            display: inline-block;
            margin: 0.5rem 0;
        }
        p {
            color: #495057;
            margin-bottom: 1.2rem;
            line-height: 1.5;
        }
        .btn-primary {
            background-color: #1B2F6B;
            border: none;
            padding: 0.7rem 1.8rem;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: #0F1C45;
            transform: translateY(-2px);
        }
        .try-again {
            margin-top: 1.5rem;
        }
        .try-again a {
            color: #1B2F6B;
            text-decoration: none;
            font-weight: 500;
            border-bottom: 1px dashed #cbd5e0;
        }
        .try-again a:hover {
            border-bottom: 1px solid #1B2F6B;
        }
        .footer-note {
            margin-top: 2rem;
            font-size: 0.75rem;
            color: #868e96;
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
            <a href="{{ url('/') }}" class="brand">Staff<span class="accent">MS</span></a>
            <a href="{{ url('/') }}" class="back-link"><i class="ti ti-arrow-left me-1"></i> Back to home</a>
        </div>
    </div>

    <div class="error-card-wrap">
        <div class="error-card">
            <div class="error-icon">
                <i class="ti ti-shield-off"></i>
            </div>
            <h3>Unable to Verify Document</h3>
            <p>The code <span class="code-highlight">{{ $code }}</span> is not recognized by the <strong>Staff Management System</strong> system.</p>
            <p>This could mean:<br>
            ✗ The code was typed incorrectly, or<br>
            ✗ The document did not originate from Staff Management System.</p>

            {{-- Closing statement, mirroring the success page's own
                 "AUTHENTIC & VERIFIED" badge (verify/letter-show.blade.php)
                 but in this page's own red/danger theme. Deliberately
                 "NOT VERIFIED" rather than "NOT AUTHENTIC" - one of the
                 two reasons listed above is a simple typo, not
                 necessarily a forged document, and this closing line
                 shouldn't accuse someone of fraud for a mistyped code. --}}
            <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-3" style="background: #fff5f5; color: #b02a37; font-weight: 800; letter-spacing: 0.04em; font-size: 0.95rem;">
                <i class="ti ti-shield-x"></i> NOT VERIFIED
            </div>
            <br>

            <a href="{{ url('/') }}" class="btn btn-primary">Return to Home</a>
            <div class="try-again">
                <a href="{{ route('verify.index') }}">Try another verification code</a>
            </div>
            <div class="footer-note">
                <i class="ti ti-lock"></i> Official documents always contain a QR code and an 8‑character code.
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
