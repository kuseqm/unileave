<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app_abbreviation() }} | Terms of Service</title>
    <link href="{{ asset('css/vendor/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/vendor/tabler-icons.min.css') }}">
    <style>
        body { background: #FDF8F0; font-family: 'Inter', Arial, sans-serif; color: #1E293B; }
        .legal-wrap { max-width: 760px; margin: 0 auto; padding: 3rem 1.5rem 4rem; }
        .legal-wrap h1 { font-size: 1.6rem; font-weight: 800; color: #1E3A5F; margin-bottom: 0.25rem; }
        .legal-wrap .updated { color: #6c757d; font-size: 0.85rem; margin-bottom: 2rem; }
        .legal-wrap h2 { font-size: 1.05rem; font-weight: 700; color: #1E3A5F; margin-top: 2rem; margin-bottom: 0.5rem; }
        .legal-wrap p, .legal-wrap li { font-size: 0.9rem; line-height: 1.7; color: #374151; }
        .legal-wrap ul { padding-left: 1.2rem; }
        .brand { font-weight: 900; font-size: 1.1rem; color: #1B1926; }
        .brand .accent { color: #96771A; }
        .back-link { font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="legal-wrap">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="brand">Staff<span class="accent">MS</span></div>
            {{-- Always opened as a new tab from the login page (target="_blank"),
                 so this tab's own history is empty - history.back() had nothing
                 to go back to and silently did nothing. Closing the tab returns
                 to that login tab directly; if the browser refuses to close a
                 tab it doesn't consider script-opened, the href takes over and
                 lands on login instead of leaving the click dead. --}}
            <a href="{{ route('login') }}" onclick="window.close();" class="back-link text-decoration-none"><i class="ti ti-arrow-left me-1"></i>Back</a>
        </div>

        <h1>Terms of Service</h1>
        <div class="updated">Last updated: September 2026 &middot; Applies to the StaffMS platform itself, separate from your institution's own HR policies.</div>

        <h2>1. What StaffMS Is</h2>
        <p>StaffMS is a staff management platform used by St. John's University of Tanzania (and other subscribing institutions) to handle leave, permission, and travel requests, staff records, ID cards, correspondence, and related HR workflows. These Terms cover your use of the platform itself; your institution's own leave, HR, and conduct policies still apply separately and take precedence on institutional matters.</p>

        <h2>2. Your Account</h2>
        <ul>
            <li>Your account is personal to you. Do not share your password or let someone else log in as you.</li>
            <li>You're responsible for keeping your password confidential and for reporting any suspected unauthorized access to your HR or IT department.</li>
            <li>Access is tied to your active staff status at your institution - it ends when your employment or affiliation ends.</li>
        </ul>

        <h2>3. Acceptable Use</h2>
        <ul>
            <li>Use the platform only for legitimate work purposes - submitting and reviewing requests, managing records you're authorized to see, and related institutional business.</li>
            <li>Don't attempt to access data, requests, or records that aren't yours or aren't part of your approval or oversight role.</li>
            <li>Information you submit (leave requests, letters, personal details) should be accurate and truthful.</li>
        </ul>

        <h2>4. Availability</h2>
        <p>StaffMS is provided on an as-is basis. It may occasionally be unavailable for maintenance or updates; we aim to keep disruption minimal.</p>

        <h2>5. Changes to These Terms</h2>
        <p>These Terms may be updated from time to time. Continued use of the platform after a change means you accept the updated Terms. You'll be asked to re-confirm acceptance if a change is significant.</p>

        <h2>6. Questions</h2>
        <p>Questions about these Terms should be directed to your institution's HR or IT department.</p>

        <p class="mt-4"><a href="{{ route('legal.privacy') }}">Read the Privacy Policy &rarr;</a></p>
    </div>
</body>
</html>
