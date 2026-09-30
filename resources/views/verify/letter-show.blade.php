<?php
    // Multi-tenant: this page verifies documents from any university on
    // the platform, so its own accent colors follow whichever institution
    // issued the specific document being verified - not a fixed brand.
    // Reuses the SAME two fields the letter's own header/footer crest
    // lines already use (primary_line_color/secondary_line_color, see
    // header.blade.php), not the separate primary_color/secondary_color
    // pair - those two are less consistently set and don't match what's
    // actually printed on the letter itself, which is the whole point of
    // this page looking like it belongs to the same document.
    $hexToRgb = function (string $hex): array {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    };
    $mix = function (array $rgb, array $target, float $amount) {
        return array_map(fn ($c, $t) => (int) round($c + ($t - $c) * $amount), $rgb, $target);
    };
    $rgbToHex = function (array $rgb) {
        return '#'.implode('', array_map(fn ($c) => str_pad(dechex(max(0, min(255, $c))), 2, '0', STR_PAD_LEFT), $rgb));
    };

    $brandPrimary = ($university ?? null)?->primary_line_color ?: '#1B2F6B';
    $brandSecondary = ($university ?? null)?->secondary_line_color ?: '#C9A227';
    $primaryRgb = $hexToRgb($brandPrimary);
    $brandPrimaryDeep = $rgbToHex($mix($primaryRgb, [0, 0, 0], 0.35));
    $brandPrimarySoft = $rgbToHex($mix($primaryRgb, [255, 255, 255], 0.9));
    $secondaryRgb = $hexToRgb($brandSecondary);
    $brandSecondaryDeep = $rgbToHex($mix($secondaryRgb, [0, 0, 0], 0.3));

    $logoUrl = ($university ?? null)?->logo_path
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($university->logo_path)
        : null;
?>
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
            --navy: {{ $brandPrimary }};
            --navy-deep: {{ $brandPrimaryDeep }};
            --navy-soft: {{ $brandPrimarySoft }};
            --gold: {{ $brandSecondary }};
            --gold-deep: {{ $brandSecondaryDeep }};
        }
        * { font-family: 'Public Sans', sans-serif; }
        body {
            background: var(--paper);
            color: var(--ink);
            padding-bottom: 40px;
        }

        /* --- Photo header, same rotating-slide treatment as the report
           verification page (verify/public-show.blade.php) and the landing
           page hero - one consistent look across every verification
           result, regardless of what kind of document it verifies. --- */
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
            max-width: 700px;
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
        .meta-row {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.65rem 0;
            border-bottom: 1px solid var(--line);
            font-size: 0.92rem;
        }
        .meta-row:last-child { border-bottom: none; }
        .meta-row .label { color: var(--ink-soft); }
        .meta-row .value { font-weight: 600; text-align: right; }
        .btn { border-radius: 6px; }
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
                @if($logoUrl)
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="{{ $logoUrl }}" alt="{{ $issuedBy }} logo" style="height: 44px; width: 44px; object-fit: contain;">
                    <span style="font-weight: 700; color: var(--navy);">{{ $issuedBy }}</span>
                </div>
                @endif
                <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
                    <i class="ti ti-check"></i>
                    <div>This document is genuine and was issued by the Staff Management System.</div>
                </div>

                {{-- A letter's content is confidential and belongs only to
                     its recipient and the offices that approved it - this
                     page proves the document is real without disclosing
                     what it says or who it was written to. Issued To /
                     Approved By are the one deliberate exception: whoever
                     is checking a code already has the physical/digital
                     letter in hand (the code isn't guessable or
                     searchable), so confirming the name on THAT letter
                     matches what the institution's own records say isn't
                     a new disclosure - it's the actual point of a legally
                     useful verification, the same way a degree
                     verification portal confirms a graduate's name
                     against their certificate. --}}
                <div class="meta-row">
                    <span class="label">Document Type</span>
                    <span class="value">{{ $documentType }}</span>
                </div>
                <div class="meta-row">
                    <span class="label">Reference Number</span>
                    <span class="value">{{ $letterNumber }}</span>
                </div>
                @isset($recipientName)
                <div class="meta-row">
                    <span class="label">Issued To</span>
                    <span class="value">{{ $recipientName }}</span>
                </div>
                @endisset
                @if(!empty($programme))
                <div class="meta-row">
                    <span class="label">Programme</span>
                    <span class="value">{{ $programme }}</span>
                </div>
                @endif
                @if(!empty($graduationYear))
                <div class="meta-row">
                    <span class="label">Graduation Year</span>
                    <span class="value">{{ $graduationYear }}</span>
                </div>
                @endif
                @if(!empty($certificateNumber))
                <div class="meta-row">
                    <span class="label">Certificate Number</span>
                    <span class="value">{{ $certificateNumber }}</span>
                </div>
                @endif
                @if(!empty($verifiedGraduates))
                <div class="meta-row">
                    <span class="label">Graduates Verified</span>
                    <span class="value">{!! implode('<br>', array_map('e', $verifiedGraduates)) !!}</span>
                </div>
                @endif
                @isset($approvedBy)
                <div class="meta-row">
                    <span class="label">Approved By</span>
                    <span class="value">{{ $approvedBy }}</span>
                </div>
                @endisset
                <div class="meta-row">
                    <span class="label">Verification Code</span>
                    <span class="value">{{ $secureCode }}</span>
                </div>
                <div class="meta-row">
                    <span class="label">Issued By</span>
                    <span class="value">{{ $issuedBy }}</span>
                </div>
                <div class="meta-row">
                    <span class="label">Issued On</span>
                    <span class="value">{{ $issuedOn }}</span>
                </div>
                <div class="meta-row">
                    <span class="label">Verified At</span>
                    <span class="value">{{ $verifiedAt }}</span>
                </div>

                {{-- Closing confirmation, after every detail above has
                     been read - not just the opening banner before them.
                     User asked for this specifically: a statement that
                     the document is genuine/authentic, repeated at the
                     end, so the reader's last impression (after checking
                     the name/reference number/dates against their own
                     copy) is the confirmation itself, not just a claim
                     made before any of the proof was shown. --}}
                <div class="text-center mt-4 pt-3" style="border-top: 1px solid var(--line);">
                    <div style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1.25rem; border-radius: 999px; background: var(--navy-soft); color: var(--navy-deep); font-weight: 800; letter-spacing: 0.04em; font-size: 0.95rem;">
                        <i class="ti ti-shield-check"></i> AUTHENTIC &amp; VERIFIED
                    </div>
                </div>

                <div class="mt-4 text-center">
                    @isset($printUrl)
                        <a href="{{ $printUrl }}" class="btn btn-primary px-4 me-2" style="background-color: var(--navy); border-color: var(--navy);">Print Certificate</a>
                    @endisset
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary">Back to Home</a>
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
