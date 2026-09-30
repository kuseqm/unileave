<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app_abbreviation() }} | Privacy Policy</title>
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
            {{-- Same fix as terms.blade.php - always opened as a new tab from
                 login (target="_blank"), so history.back() had nothing to go
                 back to. Closing the tab returns to that login tab directly;
                 the href is only a fallback if the browser won't close it. --}}
            <a href="{{ route('login') }}" onclick="window.close();" class="back-link text-decoration-none"><i class="ti ti-arrow-left me-1"></i>Back</a>
        </div>

        <h1>Privacy Policy</h1>
        <div class="updated">Last updated: September 2026 &middot; Applies to the StaffMS platform itself.</div>

        <h2>1. What We Collect</h2>
        <ul>
            <li>Identity and contact details: name, staff ID, email, phone, department, and role.</li>
            <li>Employment records: leave, permission, and travel history, contracts, and related documents you or HR upload.</li>
            <li>Request content: what you write in leave/travel letters, handover notes, and reasons for a request.</li>
            <li>Signatures you draw when signing a letter, and the resulting signed documents.</li>
            <li>Login activity: timestamps, IP address, and an audit trail of significant actions taken in the system.</li>
        </ul>

        <h2>2. Why We Collect It</h2>
        <ul>
            <li>To process and route your requests through the correct approval chain.</li>
            <li>To generate official letters and records tied to your employment.</li>
            <li>To keep an accurate, auditable record of staff activity for compliance and dispute resolution.</li>
            <li>To secure accounts and detect unauthorized access.</li>
        </ul>

        <h2>3. Who Can See It</h2>
        <p>Your information is visible to you, to the specific people your request needs to pass through for approval (e.g. your Head of Department, Dean, HR, and DVC, depending on the request type), to a colleague who accepts to hold your position while you're away (limited to what they need, such as your handover notes), and to system administrators for support and audit purposes. If StaffMS serves more than one institution, one institution's records are never visible to another.</p>

        <h2>4. Storage &amp; Retention</h2>
        <p>Data is stored within your institution's StaffMS database and retained according to your institution's own record-keeping requirements for staff and HR records.</p>

        <h2>5. Security Measures</h2>
        <ul>
            <li>Passwords are stored hashed, never in plain text.</li>
            <li>Access to each page and record is controlled by your role and relationship to the request.</li>
            <li>Repeated failed login attempts temporarily lock an account to resist guessing attacks.</li>
            <li>Significant actions (approvals, rejections, record changes) are logged with who did what and when, for non-repudiation.</li>
        </ul>

        <h2>6. Your Rights</h2>
        <p>You can review your own records within the system at any time. To correct inaccurate information or ask a question about data held about you, contact your institution's HR or IT department.</p>

        <h2>7. Contact</h2>
        <p>Privacy questions should be directed to your institution's HR or IT department.</p>

        <p class="mt-4"><a href="{{ route('legal.terms') }}">Read the Terms of Service &rarr;</a></p>
    </div>
</body>
</html>
