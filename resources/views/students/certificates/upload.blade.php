@extends('layouts.master')

@section('title', 'Upload Certificate Numbers')

@section('content')
<div class="container-fluid px-2">
    <x-page-header title="UPLOAD CERTIFICATE NUMBERS" description="Attach certificate numbers to an existing graduation year, programme and campus.">
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Graduates
        </a>
    </x-page-header>

    @if (session('report'))
        @php($report = session('report'))
        <div class="card rounded-sm mb-3">
            <div class="card-body py-2 px-3" style="font-size: 0.75rem;">
                <div class="d-flex gap-2 flex-wrap mb-1">
                    <span class="badge bg-success-subtle text-success rounded-sm"><i class="ti ti-check me-1"></i>{{ number_format($report['updated']) }} updated</span>
                    <span class="badge bg-warning-subtle text-warning rounded-sm"><i class="ti ti-player-skip-forward me-1"></i>{{ number_format($report['skipped']) }} skipped</span>
                    <span class="badge bg-danger-subtle text-danger rounded-sm"><i class="ti ti-search-off me-1"></i>{{ number_format($report['not_found']) }} not found</span>
                    <span class="badge bg-danger-subtle text-danger rounded-sm"><i class="ti ti-alert-triangle me-1"></i>{{ number_format($report['failed'] ?? 0) }} failed</span>
                </div>
                @if (! empty($report['flagged']))
                    <div class="mt-2 border-top pt-2">
                        <div class="fw-semibold mb-1"><i class="ti ti-flag text-danger me-1"></i>Flagged rows</div>
                        <ul class="mb-0 ps-3" style="max-height: 200px; overflow-y: auto;">
                            @foreach (array_slice($report['flagged'], 0, 20) as $flag)
                                <li>Row {{ $flag['row'] }} (<code>{{ $flag['registration_number'] }}</code>) — {{ $flag['reason'] }}</li>
                            @endforeach
                            @if (count($report['flagged']) > 20)
                                <li>... and {{ count($report['flagged']) - 20 }} more</li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger py-2 px-2 rounded-sm mb-3" style="font-size: 0.75rem;">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="certUploadWarning" class="d-none">
        <x-alert-warning :session="false" :dismissible="true">
            <span id="certUploadWarningText"></span>
        </x-alert-warning>
    </div>

    <div id="stat-scope-label" class="text-muted mb-1" style="font-size: 0.65rem;">
        <i class="ti ti-filter me-1"></i>Showing: all years · all programmes · all campuses
    </div>
    <div class="row g-2 mb-3">
        <x-stats-card value-id="stat-total" value="{{ number_format($stats['total']) }}" label="Graduates" color="primary" icon="school" />
        <x-stats-card value-id="stat-with-certificate" value="{{ number_format($stats['with_certificate']) }}" label="With Certificate" color="success" icon="certificate" />
        <x-stats-card value-id="stat-pending" value="{{ number_format($stats['pending']) }}" label="Pending" color="warning" icon="clock" />
        <x-stats-card value-id="stat-coverage" value="{{ $stats['total'] > 0 ? round(($stats['with_certificate'] / $stats['total']) * 100) : 0 }}%" label="Coverage" color="info" icon="percentage" />
    </div>

    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center">
                    <i class="ti ti-file-upload text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Upload Certificate Numbers</span>
                </div>
                <div class="card-body p-3">
                    <x-leave-wizard-steps :current-step="1" :steps="[
                        1 => ['label' => 'Choose Details', 'icon' => 'ti-list-check', 'color' => '#5B84C4', 'pulse' => '91,132,196'],
                        2 => ['label' => 'Download & Fill Template', 'icon' => 'ti-file-spreadsheet', 'color' => '#B88F42', 'pulse' => '184,143,66'],
                        3 => ['label' => 'Upload', 'icon' => 'ti-upload', 'color' => '#4C9C79', 'pulse' => '76,156,121'],
                    ]" />

                    <form method="POST" action="{{ route('students.certificates.store') }}" enctype="multipart/form-data" id="certificateUploadForm" class="mt-3">
                        @csrf
                        <p class="text-muted mb-2" style="font-size: 0.65rem;">Pick the Year and Programme once, download a worklist already filled with who's still pending, type the certificate numbers in, then upload it back.</p>

                        @can('import_students')
                        <x-alert-light class="mb-3 py-2 px-2 rounded-sm">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-help-circle text-warning" style="font-size: 1.1rem;"></i>
                                    <div>
                                        <strong class="d-block" style="font-size: 0.7rem;">Can't find your Programme or Graduation Year?</strong>
                                        <span class="text-muted" style="font-size: 0.62rem;">Add the missing one to the Academic Catalog first, then come back and pick it below.</span>
                                    </div>
                                </div>
                                <a href="{{ route('students.catalog.index') }}" target="_blank" class="btn btn-warning btn-sm rounded-sm text-nowrap" style="font-size: 0.65rem;">
                                    <i class="ti ti-plus me-1"></i> Open Academic Catalog
                                </a>
                            </div>
                        </x-alert-light>
                        @endcan

                        <div class="d-flex align-items-center gap-1 mb-2">
                            <span class="badge rounded-pill bg-primary" style="font-size: 0.6rem; width: 1.1rem; height: 1.1rem; padding: 0;">1</span>
                            <span class="text-uppercase text-muted" style="font-size: 0.6rem; letter-spacing: 0.03em;">Graduation Year &amp; Programme</span>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <x-scrollable-dropdown
                                    name="graduation_year"
                                    id="graduation_year"
                                    label="Graduation Year"
                                    icon="ti-calendar-event"
                                    :required="true"
                                    placeholder="Select Year"
                                    :options="$graduationCeremonies->map(fn ($ceremony) => ['value' => $ceremony->year, 'label' => $ceremony->year.' — Ceremony No. '.$ceremony->sequence_number])->all()"
                                    :value="old('graduation_year')"
                                    help-text="Only years with a recorded Graduation Ceremony are listed."
                                />
                            </div>
                            <div class="col-md-6">
                                <x-scrollable-dropdown
                                    name="program_id"
                                    id="program_id"
                                    label="Programme"
                                    icon="ti-book"
                                    :required="true"
                                    placeholder="Select Programme"
                                    :options="$programs->map(fn ($program) => ['value' => $program->id, 'label' => $program->degree_abbreviation ?? $program->code ?? $program->name])->all()"
                                    :value="old('program_id')"
                                />
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-1 mb-2">
                            <span class="badge rounded-pill bg-primary" style="font-size: 0.6rem; width: 1.1rem; height: 1.1rem; padding: 0;">2</span>
                            <span class="text-uppercase text-muted" style="font-size: 0.6rem; letter-spacing: 0.03em;">Campus (Optional)</span>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <x-scrollable-dropdown
                                    name="campus_id"
                                    id="campus_id"
                                    label="Campus"
                                    icon="ti-map-pin"
                                    placeholder="All / auto-detect"
                                    :options="$campuses->map(fn ($campus) => ['value' => $campus->id, 'label' => $campus->name])->all()"
                                    :value="old('campus_id')"
                                    help-text="Leave empty to auto-detect when the student has only one matching graduation."
                                />
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-1 mb-2">
                            <span class="badge rounded-pill bg-secondary" style="font-size: 0.6rem; width: 1.1rem; height: 1.1rem; padding: 0;">3</span>
                            <span class="text-uppercase text-muted" style="font-size: 0.6rem; letter-spacing: 0.03em;">The Filled-In Certificate File</span>
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label" style="font-size: 0.65rem;">
                                <i class="ti ti-paperclip me-1 text-muted"></i>Certificate File (CSV / XLSX) <span class="text-danger">*</span>
                            </label>
                            <input type="file" id="file" name="file" class="form-control form-control-sm rounded-sm @error('file') is-invalid @enderror" accept=".csv,.xlsx" required>
                            @error('file')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            <small class="text-muted d-block" style="font-size: 0.6rem;">Registration number, then certificate number. Max 10&nbsp;MB.</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" formaction="{{ route('students.certificates.template') }}" formmethod="GET" formnovalidate class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-download me-1"></i> Download Template
                            </button>
                            <button type="submit" id="certificateUploadBtn" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-upload me-1"></i> Upload
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2" style="font-size: 0.6rem;">Download Template uses the Year/Programme/Campus picked above and comes pre-filled with graduates still missing a certificate number.</small>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">How It Works</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <ul class="mb-2 ps-3" style="font-size: 0.65rem; color: #555;">
                        <li>Pick the Graduation Year and Programme the certificates belong to, then download a template scoped to that batch - the numbers above update to match too.</li>
                        <li>Campus can stay on <strong>All / auto-detect</strong> when a student has only one graduation for that year and programme.</li>
                        <li>The template comes back pre-filled with every graduate still missing a certificate number - just type the numbers into column B.</li>
                        <li>A row is <strong>skipped</strong> when its certificate cell is blank or already matches what's on file - never blanked out by a re-upload.</li>
                        <li>A row is <strong>failed</strong> when its certificate number is already assigned to someone else.</li>
                    </ul>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Download Template is a worklist, not a blank form</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Re-downloading and re-uploading the same reference keeps everything under one tracked batch.</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>

    @if($activeBatches->total() + $closedBatches->total() > 0)
    <div class="card rounded-sm mb-3">
        <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between">
            <span class="d-flex align-items-center">
                <i class="ti ti-history text-secondary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Certificate Upload History</span>
            </span>
            <ul class="nav nav-pills gap-1" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link py-0 px-2 {{ $tab === 'active' ? 'active' : '' }}" style="font-size: 0.65rem;" data-bs-toggle="pill" data-bs-target="#certHistoryActive" role="tab">
                        Active <span class="badge bg-secondary-subtle text-secondary rounded-sm">{{ $activeBatches->total() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link py-0 px-2 {{ $tab === 'closed' ? 'active' : '' }}" style="font-size: 0.65rem;" data-bs-toggle="pill" data-bs-target="#certHistoryClosed" role="tab">
                        Closed <span class="badge bg-secondary-subtle text-secondary rounded-sm">{{ $closedBatches->total() }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade {{ $tab === 'active' ? 'show active' : '' }}" id="certHistoryActive" role="tabpanel">
                @include('students.certificates._history-table', ['batches' => $activeBatches, 'emptyText' => 'No open references.'])
                <div class="p-2 border-top">
                    <x-pagination :items="$activeBatches" id="certHistory" pageParam="history_page" perPageParam="history_per_page" :perPageOptions="[10, 25, 50]" />
                </div>
            </div>
            <div class="tab-pane fade {{ $tab === 'closed' ? 'show active' : '' }}" id="certHistoryClosed" role="tabpanel">
                @include('students.certificates._history-table', ['batches' => $closedBatches, 'emptyText' => 'No closed references yet - closed references move here.'])
                <div class="p-2 border-top">
                    <x-pagination :items="$closedBatches" id="certClosed" pageParam="closed_page" perPageParam="closed_per_page" :perPageOptions="[10, 25, 50]" />
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Certificate Status starts as a whole-university total (see the
        // controller's index()) - once a Year/Programme/Campus is picked,
        // re-fetch it scoped to that selection so "confirm your file is
        // complete before uploading" is actually true for THIS batch.
        const refreshCertStats = function () {
            const year = document.getElementById('graduation_year').value;
            const programId = document.getElementById('program_id').value;
            const campusId = document.getElementById('campus_id').value;

            const params = new URLSearchParams();
            if (year) params.set('graduation_year', year);
            if (programId) params.set('program_id', programId);
            if (campusId) params.set('campus_id', campusId);

            fetch("{{ route('students.certificates.stats') }}?" + params.toString(), {
                headers: {'Accept': 'application/json'},
            })
                .then(response => response.json())
                .then(function (data) {
                    document.getElementById('stat-total').textContent = data.total.toLocaleString();
                    document.getElementById('stat-with-certificate').textContent = data.with_certificate.toLocaleString();
                    document.getElementById('stat-pending').textContent = data.pending.toLocaleString();
                    document.getElementById('stat-coverage').textContent = data.coverage + '%';

                    const scope = [
                        year || 'all years',
                        programId ? document.getElementById('program_id_toggle').textContent.trim() : 'all programmes',
                        campusId ? document.getElementById('campus_id_toggle').textContent.trim() : 'all campuses',
                    ];
                    document.getElementById('stat-scope-label').innerHTML = '<i class="ti ti-filter me-1"></i>Showing: ' + scope.join(' · ');
                })
                .catch(function () {
                    // Leave the last known numbers on screen rather than blanking them.
                });
        };

        wireDropdown('graduation_year_menu', 'graduation_year', 'graduation_year_toggle', 'Select Year', refreshCertStats);
        wireDropdown('program_id_menu', 'program_id', 'program_id_toggle', 'Select Programme', refreshCertStats);
        wireDropdown('campus_id_menu', 'campus_id', 'campus_id_toggle', 'All / auto-detect', refreshCertStats);

        // Download Template shares this same <form> (via formaction/
        // formmethod overrides) so it always downloads a template scoped to
        // whatever Year/Programme/Campus is currently picked - same pattern
        // as the Graduation Roster page. Only the Upload submit is
        // intercepted for the required-field check, since Campus/Programme/
        // Year are hidden inputs the browser's native `required` never sees.
        const form = document.getElementById('certificateUploadForm');
        const uploadBtn = document.getElementById('certificateUploadBtn');

        form.addEventListener('submit', function (event) {
            const submitter = event.submitter;
            if (submitter && submitter !== uploadBtn) {
                return; // Download Template button - let the normal GET proceed.
            }

            if (!document.getElementById('graduation_year').value || !document.getElementById('program_id').value) {
                event.preventDefault();
                const warningEl = document.getElementById('certUploadWarning');
                const warningText = document.getElementById('certUploadWarningText');
                if (warningEl && warningText) {
                    warningText.textContent = 'Please select a Graduation Year and a Programme first.';
                    warningEl.classList.remove('d-none');
                    warningEl.scrollIntoView({behavior: 'smooth', block: 'start'});
                }
            }
        });
    });
</script>
@endsection
