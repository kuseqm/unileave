@extends('layouts.master')

@section('title', 'ID Card Preview')

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'ID Cards', 'url' => route('admin.id-cards.index')],
        ['name' => 'Preview', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="ID CARD PREVIEW" description="{{ $idCard->staff->full_name }} — {{ $idCard->staff->staff_id }}">
        <a href="{{ route('id-cards.print', $idCard->id) }}?autoprint=1" target="_blank" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-printer me-1"></i> Print
        </a>
        <a href="{{ route('admin.id-cards.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>

    <div class="row g-2">
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-id text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Card Preview</span>
                    </div>
                </div>
                <div class="card-body py-3 px-2 text-center" style="background: #f1f5f9;">
                    <iframe id="idCardPreviewFrame" src="{{ route('id-cards.print', $idCard->id) }}"
                            style="width: 100%; height: 400px; border: none; display: block;"
                            title="ID Card Preview"></iframe>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Card Details</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                        <tr>
                            <th style="width: 40%;">Staff ID</th>
                            <td>{{ $idCard->staff->staff_id }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $idCard->staff->full_name }}</td>
                        </tr>
                        <tr>
                            <th>Job Title</th>
                            <td>{{ $idCard->staff->job_title }}</td>
                        </tr>
                        <tr>
                            <th>Issued</th>
                            <td>{{ $idCard->issued_at?->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Valid Until</th>
                            <td>{{ $idCard->valid_until?->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Signed By</th>
                            <td>{{ $idCard->authorized_by ?? $idCard->authorizedSignature->name ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// The print view's card scales itself to fit the iframe's width (see
// print.blade.php) - its actual height changes with that scale, so a fixed
// iframe height either clips it or leaves a lot of empty space below.
// Same-origin, so the real content height is readable directly.
(function () {
    const frame = document.getElementById('idCardPreviewFrame');

    function resizeFrame() {
        try {
            const doc = frame.contentDocument || frame.contentWindow.document;
            const height = doc.documentElement.scrollHeight;
            if (height > 0) {
                frame.style.height = (height + 40) + 'px';
            }
        } catch (e) {
            // Cross-origin or not ready yet - leave the fallback height.
        }
    }

    frame.addEventListener('load', function () {
        resizeFrame();
        // The print view's own script also runs a fit-to-width pass after
        // load (fonts/QR code settling) - catch that follow-up resize too.
        setTimeout(resizeFrame, 300);
    });
    window.addEventListener('resize', resizeFrame);
})();
</script>
@endsection
