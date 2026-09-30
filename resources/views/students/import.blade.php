@extends('layouts.master')

@section('title', 'Import Graduation Roster')

@section('content')
<div class="container-fluid px-2">

    <x-page-header title="IMPORT GRADUATION ROSTER" description="Bulk-register graduates from the registry's graduation books">
        <a href="{{ route('students.import.updates') }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-history-toggle me-1"></i> Recent Updates
        </a>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Graduates
        </a>
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div id="importWarning" class="d-none">
        <x-alert-warning :session="false" :dismissible="true">
            <span id="importWarningText"></span>
        </x-alert-warning>
    </div>

    @if(session('importReport'))
        @php($report = session('importReport'))
        <div class="card rounded-sm mb-3">
            <div class="card-header py-1 px-2 bg-light">
                <span style="font-size: 0.7rem; font-weight: 600;">Import Results</span>
            </div>
            <div class="card-body p-3">
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <span class="badge bg-success rounded-sm" style="font-size: 0.7rem;">{{ $report['created'] }} newly added</span>
                    </div>
                    <div class="col-md-4">
                        <span class="badge bg-info rounded-sm" style="font-size: 0.7rem;">{{ $report['updated'] }} updated</span>
                    </div>
                    <div class="col-md-4">
                        <span class="badge bg-warning text-dark rounded-sm" style="font-size: 0.7rem;">{{ count($report['flagged']) }} need review</span>
                    </div>
                </div>

                @if(count($report['flagged']) > 0)
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size: 0.7rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Row</th>
                                <th>Registration Number</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report['flagged'] as $item)
                            <tr>
                                <td>{{ $item['row'] }}</td>
                                <td><code>{{ $item['registration_number'] }}</code></td>
                                <td>{{ $item['reason'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    @endif

    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center">
                    <i class="ti ti-file-upload text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Upload Excel</span>
                </div>
                <div class="card-body p-3">
                    <x-leave-wizard-steps :current-step="1" :steps="[
                        1 => ['label' => 'Choose Details', 'icon' => 'ti-list-check', 'color' => '#5B84C4', 'pulse' => '91,132,196'],
                        2 => ['label' => 'Download & Fill Template', 'icon' => 'ti-file-spreadsheet', 'color' => '#B88F42', 'pulse' => '184,143,66'],
                        3 => ['label' => 'Upload', 'icon' => 'ti-upload', 'color' => '#4C9C79', 'pulse' => '76,156,121'],
                    ]" />

                    <form action="{{ route('students.import.upload') }}" method="POST" enctype="multipart/form-data" id="rosterImportForm" class="mt-3">
                        @csrf
                        <p class="text-muted mb-2" style="font-size: 0.65rem;">A graduation book is normally one Campus, one Programme and one Graduation Year - pick them once here, and the template/upload only need per-student rows.</p>

                        @can('import_students')
                        <x-alert-light class="mb-3 py-2 px-2 rounded-sm">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-help-circle text-warning" style="font-size: 1.1rem;"></i>
                                    <div>
                                        <strong class="d-block" style="font-size: 0.7rem;">Can't find your Faculty/School, Programme or Graduation Year?</strong>
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
                            <span class="text-uppercase text-muted" style="font-size: 0.6rem; letter-spacing: 0.03em;">Campus Attended</span>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <x-scrollable-dropdown
                                    name="campus_id"
                                    id="campus_id"
                                    label="Campus"
                                    icon="ti-map-pin"
                                    :required="true"
                                    placeholder="Select Campus"
                                    :options="$campuses->map(fn ($campus) => ['value' => $campus->id, 'label' => $campus->name])->all()"
                                    help-text="Where these graduates physically studied - independent of Faculty/Programme, which are offered across every campus."
                                />
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-1 mb-2">
                            <span class="badge rounded-pill bg-primary" style="font-size: 0.6rem; width: 1.1rem; height: 1.1rem; padding: 0;">2</span>
                            <span class="text-uppercase text-muted" style="font-size: 0.6rem; letter-spacing: 0.03em;">Faculty &amp; Programme</span>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-5">
                                <x-scrollable-dropdown
                                    id="school_id"
                                    label="Faculty/School"
                                    icon="ti-building-arch"
                                    :required="true"
                                    placeholder="Select Faculty/School"
                                    :options="$schools->map(fn ($school) => ['value' => $school->id, 'label' => $school->code ?: $school->name, 'title' => $school->name])->all()"
                                />
                            </div>
                            <div class="col-md-7">
                                <x-scrollable-dropdown
                                    name="program_id"
                                    id="program_id"
                                    label="Programme"
                                    icon="ti-book"
                                    :required="true"
                                    :disabled="true"
                                    placeholder="Select Faculty/School First"
                                    :options="[]"
                                />
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-1 mb-2">
                            <span class="badge rounded-pill bg-primary" style="font-size: 0.6rem; width: 1.1rem; height: 1.1rem; padding: 0;">3</span>
                            <span class="text-uppercase text-muted" style="font-size: 0.6rem; letter-spacing: 0.03em;">Graduation Year</span>
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
                                    help-text="Only years with a recorded Graduation Ceremony are listed - add a new one first if the year you need isn't here."
                                />
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-1 mb-2">
                            <span class="badge rounded-pill bg-secondary" style="font-size: 0.6rem; width: 1.1rem; height: 1.1rem; padding: 0;">4</span>
                            <span class="text-uppercase text-muted" style="font-size: 0.6rem; letter-spacing: 0.03em;">The Filled-In Roster</span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size: 0.65rem;"><i class="ti ti-paperclip me-1 text-muted"></i>Excel File (.xlsx / .csv) <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control form-control-sm rounded-sm @error('file') is-invalid @enderror" accept=".xlsx,.csv" required>
                            @error('file')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" formaction="{{ route('students.import.template') }}" formmethod="GET" formnovalidate class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-download me-1"></i> Download Template
                            </button>
                            <button type="submit" id="uploadSubmitBtn" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-upload me-1"></i> Upload
                            </button>
                        </div>

                        <div id="uploadProgressWrap" class="d-none mt-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted" style="font-size: 0.6rem;" id="uploadProgressLabel">Uploading...</span>
                                <span class="text-muted fw-semibold" style="font-size: 0.6rem;" id="uploadProgressPercent">0%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($recentImports->isNotEmpty() && $latestBatch)
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between">
                    <span style="font-size: 0.7rem; font-weight: 600;">Latest Upload - Graduates</span>
                    <span class="text-muted" style="font-size: 0.65rem;">
                        <code>{{ $latestBatch->reference_number }}</code> · showing {{ $recentImports->count() }} of {{ $latestBatch->students_count }}
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Reg No</th>
                                <th>Name</th>
                                <th>Campus</th>
                                <th>Programme</th>
                                <th>Graduation Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentImports as $student)
                            <tr>
                                <td><code>{{ $student->registration_number }}</code></td>
                                <td><a href="{{ route('students.show', $student->id) }}" class="text-decoration-none">{{ $student->formatted_name }}</a></td>
                                <td>{{ $student->campus->name ?? '—' }}</td>
                                <td>{{ $student->program?->degree_abbreviation ?? $student->program?->code ?? $student->program?->name ?? '—' }}</td>
                                <td>{{ $student->graduation_year ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
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
                    <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                        <li>Pick Campus, Faculty/School, Programme and Graduation Year once, then download a template scoped to that batch.</li>
                        <li>Each row then only needs: Registration Number, Surname, First Name, Other Names, Sex (M/F - Me/Ke is understood too), Certificate Number (optional).</li>
                        <li>Start/End Academic Year are calculated automatically from the Registration Number and Graduation Year.</li>
                        <li>If the Graduation Year is on file in Graduation Ceremonies, the exact ceremony date and number are filled in automatically.</li>
                        <li>For graduates before 2018, the Certificate Number is auto-filled to match the Registration Number if not provided.</li>
                        <li>A Registration Number already on file is updated, not duplicated - you can upload graduation books a little at a time.</li>
                        <li>GPA, Classification and Degree wording are filled in or updated later, when a verification request needs them.</li>
                    </ul>
                </div>
            </div>

            @if($yearStats->isNotEmpty())
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between">
                    <span class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Graduates by Year</span>
                    </span>
                    <a href="{{ route('students.statistics') }}" class="text-decoration-none" style="font-size: 0.62rem;">Full statistics <i class="ti ti-arrow-right"></i></a>
                </div>
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                        <thead class="table-light" style="position: sticky; top: 0;">
                            <tr>
                                <th>Graduation Year</th>
                                <th class="text-end">Males</th>
                                <th class="text-end">Females</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($yearStats as $row)
                            <tr>
                                <td class="fw-semibold">{{ $row->graduation_year }}</td>
                                <td class="text-end">{{ number_format($row->males) }}</td>
                                <td class="text-end">{{ number_format($row->females) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($row->total) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-semibold">
                                <td>All years</td>
                                <td class="text-end">{{ number_format($yearStats->sum('males')) }}</td>
                                <td class="text-end">{{ number_format($yearStats->sum('females')) }}</td>
                                <td class="text-end">{{ number_format($yearStats->sum('total')) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @if($unknownSexCount > 0)
                <div class="px-2 py-2 border-top" style="font-size: 0.65rem;">
                    <div class="text-muted mb-1"><i class="ti ti-alert-triangle text-warning me-1"></i>{{ number_format($unknownSexCount) }} graduate(s) have no sex recorded, so they count in Total only. Open each to set it:</div>
                    <ul class="list-unstyled mb-0">
                        @foreach($unknownSexGraduates as $graduate)
                        <li class="d-flex justify-content-between align-items-center gap-2 py-1">
                            <span><code>{{ $graduate->registration_number }}</code> {{ $graduate->formatted_name }} <span class="text-muted">· {{ $graduate->graduation_year }}</span></span>
                            <a href="{{ route('students.edit', $graduate->id) }}" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.05rem 0.4rem;">Edit</a>
                        </li>
                        @endforeach
                    </ul>
                    @if($unknownSexCount > $unknownSexGraduates->count())
                    <div class="text-muted mt-1">and {{ number_format($unknownSexCount - $unknownSexGraduates->count()) }} more.</div>
                    @endif
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <div class="row g-2 mb-2">
        <div class="col-md-6">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-search text-muted me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Trace Student in any uploaded file</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2" style="font-size: 0.7rem;">
                    <form action="{{ route('students.import.trace') }}" method="GET" class="d-flex gap-2">
                        <input type="text" name="reg" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" placeholder="Registration number e.g. 2009/0518" autocomplete="off" required>
                        <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                            <i class="ti ti-search me-1"></i> Trace
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-search text-muted me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Find any imported file or reference</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <input type="text" id="historySearchInput" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" placeholder="Type reference, programme or campus..." autocomplete="off">
                </div>
            </div>
        </div>
    </div>

    @if($activeBatches->total() + $closedBatches->total() > 0)

    <div class="card rounded-sm mb-3">
        <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between">
            <span class="d-flex align-items-center">
                <i class="ti ti-history text-secondary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Import History</span>
            </span>
            <ul class="nav nav-pills gap-1" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link py-0 px-2 {{ $tab === 'active' ? 'active' : '' }}" style="font-size: 0.65rem;" data-bs-toggle="pill" data-bs-target="#historyActive" data-history-tab="active" role="tab">
                        Active <span class="badge bg-secondary-subtle text-secondary rounded-sm">{{ $activeBatches->total() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link py-0 px-2 {{ $tab === 'closed' ? 'active' : '' }}" style="font-size: 0.65rem;" data-bs-toggle="pill" data-bs-target="#historyClosed" data-history-tab="closed" role="tab">
                        Closed <span class="badge bg-secondary-subtle text-secondary rounded-sm">{{ $closedBatches->total() }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade {{ $tab === 'active' ? 'show active' : '' }}" id="historyActive" role="tabpanel">
                @include('students._import-history-table', ['batches' => $activeBatches, 'reviewItems' => $reviewItems, 'emptyText' => 'No open references.'])
                <div class="p-2 border-top">
                    <x-pagination :items="$activeBatches" id="history" pageParam="history_page" perPageParam="history_per_page" :perPageOptions="[10, 25, 50]" />
                </div>
            </div>
            <div class="tab-pane fade {{ $tab === 'closed' ? 'show active' : '' }}" id="historyClosed" role="tabpanel">
                @include('students._import-history-table', ['batches' => $closedBatches, 'reviewItems' => $reviewItems, 'emptyText' => 'No closed references yet - closed references move here.'])
                <div class="p-2 border-top">
                    <x-pagination :items="$closedBatches" id="closed" pageParam="closed_page" perPageParam="closed_per_page" :perPageOptions="[10, 25, 50]" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewBatchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title" style="font-size: 0.8rem;"><i class="ti ti-file-info text-primary me-1"></i> <span id="viewBatchReference"></span></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-3" style="font-size: 0.7rem;">
                        <div class="row g-2 mb-2">
                            <div class="col-6"><span class="text-muted">Type:</span> <span id="viewBatchType"></span></div>
                            <div class="col-6"><span class="text-muted">When:</span> <span id="viewBatchWhen"></span></div>
                            <div class="col-6"><span class="text-muted">Campus:</span> <span id="viewBatchCampus"></span></div>
                            <div class="col-6"><span class="text-muted">Programme:</span> <span id="viewBatchProgramme"></span></div>
                            <div class="col-6"><span class="text-muted">Year:</span> <span id="viewBatchYear"></span></div>
                            <div class="col-6"><span class="text-muted">By:</span> <span id="viewBatchBy"></span></div>
                            <div class="col-6"><span class="text-muted">Status:</span> <span id="viewBatchStatus"></span></div>
                            <div class="col-6"><span class="text-muted">File:</span> <span id="viewBatchFilename"></span></div>
                        </div>
                        <div class="d-flex gap-2 mb-3" style="font-size: 0.7rem;">
                            <span class="badge bg-success rounded-sm">New: <strong id="viewBatchNew"></strong></span>
                            <span class="badge bg-info rounded-sm">Updated: <strong id="viewBatchUpdated"></strong></span>
                            <span class="badge bg-warning text-dark rounded-sm d-none" id="viewBatchReviewBadge">Review: <strong id="viewBatchReview"></strong></span>
                            <span class="badge bg-danger-subtle text-danger d-none" id="viewBatchIncomplete"></span>
                        </div>
                        <div id="viewBatchOriginalProgram" class="d-none alert alert-warning py-1 px-2 mb-2" style="font-size: 0.65rem;">
                            <i class="ti ti-arrow-back-up me-1"></i> Corrected from <strong id="viewBatchOriginalProgramName"></strong>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="#" id="viewBatchOpenStudents" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">
                                <i class="ti ti-external-link me-1"></i> Open
                            </a>
                            <button type="button" id="viewBatchReviewBtn" class="btn btn-outline-warning btn-sm rounded-sm d-none" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;" data-review-flagged>
                                <i class="ti ti-alert-triangle me-1"></i> Review
                            </button>
                            <button type="button" id="viewBatchChangeYearBtn" class="btn btn-outline-warning btn-sm rounded-sm d-none" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;" data-reassign-year>
                                <i class="ti ti-calendar-event me-1"></i> Change Year
                            </button>
                            <button type="button" id="viewBatchChangeProgrammeBtn" class="btn btn-outline-primary btn-sm rounded-sm d-none" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;" data-reassign-programme>
                                <i class="ti ti-replace me-1"></i> Change Programme
                            </button>
                            <form action="#" method="POST" id="viewBatchCloseForm" class="d-none d-inline">
                                @csrf
                                <button type="button" id="viewBatchCloseBtn" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">
                                    <i class="ti ti-check me-1"></i> Close
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reviewFlaggedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title" style="font-size: 0.8rem;"><i class="ti ti-alert-triangle text-warning me-1"></i> Rows to review in <code id="reviewReference"></code></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <p class="text-muted px-3 pt-2 mb-2" style="font-size: 0.65rem;">These graduates were imported, but something could not be worked out automatically. Open <strong>Edit</strong> on each to fix it - the reference itself stays as it is.</p>
                    <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                        <thead class="table-light"><tr><th>Row</th><th>Registration Number</th><th>What needs attention</th><th></th></tr></thead>
                        <tbody id="reviewFlaggedRows"></tbody>
                    </table>
                </div>
                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reassignProgrammeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="reassignProgrammeForm" class="modal-content">
                @csrf
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title" style="font-size: 0.8rem;"><i class="ti ti-replace me-1"></i> Change Programme for <code id="reassignReference"></code></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <p class="mb-2" style="font-size: 0.7rem;">
                        Currently <strong id="reassignCurrentProgramme"></strong>.
                        All <strong id="reassignStudentCount"></strong> graduates of this reference will be moved to the Programme you pick - no re-upload needed. Their Faculty/School follows the new Programme.
                    </p>
                    <x-scrollable-dropdown
                        name="program_id"
                        id="reassign_program_id"
                        label="New Programme"
                        icon="ti-book"
                        :required="true"
                        placeholder="Select Programme"
                        :options="$programmeOptions"
                    />
                </div>
                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" id="reassignSubmit" style="font-size: 0.7rem;" disabled>
                        <i class="ti ti-check me-1"></i> Move Graduates
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="reassignYearModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="reassignYearForm" class="modal-content">
                @csrf
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title" style="font-size: 0.8rem;"><i class="ti ti-replace me-1"></i> Change Graduation Year for <code id="reassignYearReference"></code></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <p class="mb-2" style="font-size: 0.7rem;">
                        Currently <strong id="reassignCurrentYear"></strong>.
                        All <strong id="reassignYearStudentCount"></strong> graduates of this reference will be moved to the Graduation Year you pick - no re-upload needed. Academic years and ceremony details will be updated where they were auto-derived.
                    </p>
                    <x-scrollable-dropdown
                        name="graduation_year"
                        id="reassign_graduation_year"
                        label="New Graduation Year"
                        icon="ti-calendar-event"
                        :required="true"
                        placeholder="Select Year"
                        :options="$graduationCeremonies->map(fn ($ceremony) => ['value' => $ceremony->year, 'label' => $ceremony->year.' — Ceremony No. '.$ceremony->sequence_number])->all()"
                    />
                </div>
                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" id="reassignYearSubmit" style="font-size: 0.7rem;" disabled>
                        <i class="ti ti-check me-1"></i> Move Graduates
                    </button>
                </div>
            </form>
        </div>
    </div>

    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Keeps the x-leave-wizard-steps ribbon above the form in sync with
    // real progress, instead of sitting frozen on step 1: step 2 once
    // Campus+Programme+Year are all picked, step 3 once a file is chosen.
    // This page isn't a true page-by-page wizard (Download doesn't "commit"
    // anything, the person can freely change any selection), so this is a
    // best-effort reflection of where they are, not a gate on anything.
    const wizardSteps = [
        { color: '#5B84C4', pulse: '91,132,196', icon: 'ti-list-check' },
        { color: '#B88F42', pulse: '184,143,66', icon: 'ti-file-spreadsheet' },
        { color: '#4C9C79', pulse: '76,156,121', icon: 'ti-upload' },
    ];
    const wizardRibbon = document.querySelector('.leave-wizard-ribbon');

    function renderWizardStep(currentStep) {
        if (!wizardRibbon) {
            return;
        }

        const segments = wizardRibbon.querySelectorAll('.leave-wizard-ribbon__segment');
        segments.forEach(function (segment, index) {
            segment.style.background = (index + 1) <= currentStep ? wizardSteps[index].color : '#E7E4D9';
        });

        const stepEls = wizardRibbon.querySelectorAll('.leave-wizard-ribbon__step');
        stepEls.forEach(function (stepEl, index) {
            const number = index + 1;
            const step = wizardSteps[index];
            const state = number < currentStep ? 'completed' : (number === currentStep ? 'current' : 'upcoming');
            const dot = stepEl.querySelector('.leave-wizard-ribbon__dot');
            const label = stepEl.querySelector('.leave-wizard-ribbon__label');

            dot.classList.toggle('leave-wizard-ribbon__dot--pulse', state === 'current');
            dot.style.setProperty('--pulse-color', step.pulse);
            if (state === 'upcoming') {
                dot.style.background = '#fff';
                dot.style.borderColor = '#E7E4D9';
            } else {
                dot.style.background = step.color;
                dot.style.borderColor = '#fff';
            }

            dot.innerHTML = state === 'completed'
                ? '<i class="ti ti-check"></i>'
                : `<i class="ti ${step.icon}" style="color: ${state === 'upcoming' ? '#B4AE9C' : '#fff'};"></i>`;

            label.style.color = state === 'upcoming' ? '#94A3B8' : '#1E293B';
            label.style.fontWeight = state === 'upcoming' ? '600' : '700';
        });
    }

    function updateWizardStep() {
        const detailsChosen = document.getElementById('campus_id').value
            && document.getElementById('program_id').value
            && document.getElementById('graduation_year').value;
        const fileChosen = document.querySelector('input[name="file"]').files.length > 0;

        renderWizardStep(fileChosen ? 3 : (detailsChosen ? 2 : 1));
    }

    wireDropdown('campus_id_menu', 'campus_id', 'campus_id_toggle', 'Select Campus');
    const graduationYearDropdown = wireDropdown('graduation_year_menu', 'graduation_year', 'graduation_year_toggle', 'Select Year');
    const programmeDropdown = wireDropdown('program_id_menu', 'program_id', 'program_id_toggle', 'Select Programme');

    document.querySelectorAll('[data-history-tab]').forEach(function (tabButton) {
        tabButton.addEventListener('shown.bs.tab', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabButton.dataset.historyTab);
            window.history.replaceState(null, '', url);
        });
    });

    const reviewModalElement = document.getElementById('reviewFlaggedModal');
    if (reviewModalElement) {
        const reviewModal = new bootstrap.Modal(reviewModalElement);
        const reviewRows = document.getElementById('reviewFlaggedRows');

        document.querySelectorAll('[data-review-flagged]').forEach(function (button) {
            button.addEventListener('click', function () {
                document.getElementById('reviewReference').textContent = button.dataset.reference;
                reviewRows.replaceChildren();

                JSON.parse(button.dataset.items || '[]').forEach(function (item) {
                    const row = document.createElement('tr');
                    [item.row ? item.row : '—', item.registration_number || '—', item.reason].forEach(function (text, index) {
                        const cell = document.createElement('td');
                        if (index === 1 && item.registration_number) {
                            const code = document.createElement('code');
                            code.textContent = text;
                            cell.appendChild(code);
                        } else {
                            cell.textContent = text;
                        }
                        row.appendChild(cell);
                    });

                    const action = document.createElement('td');
                    if (item.edit_url) {
                        const link = document.createElement('a');
                        link.href = item.edit_url;
                        link.className = 'btn btn-outline-primary btn-sm rounded-sm';
                        link.style.cssText = 'font-size: 0.6rem; padding: 0.1rem 0.4rem;';
                        link.textContent = 'Edit';
                        action.appendChild(link);
                    }
                    row.appendChild(action);
                    reviewRows.appendChild(row);
                });

                reviewModal.show();
            });
        });
    }

    const reassignModalElement = document.getElementById('reassignProgrammeModal');
    if (reassignModalElement) {
        const reassignSubmit = document.getElementById('reassignSubmit');
        const reassignDropdown = wireDropdown('reassign_program_id_menu', 'reassign_program_id', 'reassign_program_id_toggle', 'Select Programme', function () {
            reassignSubmit.disabled = false;
        });
        const reassignModal = new bootstrap.Modal(reassignModalElement);

        document.querySelectorAll('[data-reassign-programme]').forEach(function (button) {
            button.addEventListener('click', function () {
                document.getElementById('reassignProgrammeForm').action = button.dataset.action;
                document.getElementById('reassignReference').textContent = button.dataset.reference;
                document.getElementById('reassignCurrentProgramme').textContent = button.dataset.programme;
                document.getElementById('reassignStudentCount').textContent = button.dataset.students;
                reassignDropdown.reset();
                reassignSubmit.disabled = true;
                reassignModal.show();
            });
        });
    }

    const reassignYearModalElement = document.getElementById('reassignYearModal');
    if (reassignYearModalElement) {
        const reassignYearSubmit = document.getElementById('reassignYearSubmit');
        const reassignYearDropdown = wireDropdown('reassign_graduation_year_menu', 'reassign_graduation_year', 'reassign_graduation_year_toggle', 'Select Year', function () {
            reassignYearSubmit.disabled = false;
        });
        const reassignYearModal = new bootstrap.Modal(reassignYearModalElement);

        document.querySelectorAll('[data-reassign-year]').forEach(function (button) {
            button.addEventListener('click', function () {
                document.getElementById('reassignYearForm').action = button.dataset.action;
                document.getElementById('reassignYearReference').textContent = button.dataset.reference;
                document.getElementById('reassignCurrentYear').textContent = button.dataset.year;
                document.getElementById('reassignYearStudentCount').textContent = button.dataset.students;
                reassignYearDropdown.reset();
                reassignYearSubmit.disabled = true;
                reassignYearModal.show();
            });
        });
    }

    // Attached after the wireDropdown() calls above (not before) so that
    // by the time this fires, the hidden inputs already hold the
    // just-clicked value - attaching it earlier would read the stale
    // pre-click value, since listeners run in attachment order.
    ['campus_id_menu', 'program_id_menu', 'graduation_year_menu'].forEach(function (menuId) {
        document.getElementById(menuId).addEventListener('click', updateWizardStep);
    });
    document.querySelector('input[name="file"]').addEventListener('change', updateWizardStep);

    // Restore an old() graduation_year selection (e.g. after a validation
    // error redirect) by finding the matching item's label.
    const oldGraduationYear = document.getElementById('graduation_year').value;
    if (oldGraduationYear) {
        const match = document.querySelector(`#graduation_year_menu .dropdown-item[data-value="${oldGraduationYear}"]`);
        if (match) {
            document.getElementById('graduation_year_toggle').textContent = match.textContent;
        }
    }

    const programmeToggle = document.getElementById('program_id_toggle');
    const programmeMenu = document.getElementById('program_id_menu');

    wireDropdown('school_id_menu', 'school_id', 'school_id_toggle', 'Select Faculty/School', function (schoolId) {
        if (!schoolId) {
            programmeMenu.innerHTML = '';
            programmeToggle.disabled = true;
            programmeDropdown.reset('Select Faculty/School First');
            return;
        }

        fetch(`{{ url('students/import/programmes') }}/${schoolId}`)
            .then(response => response.json())
            .then(programmes => {
                programmeMenu.innerHTML = programmes.map(programme =>
                    `<li><button type="button" class="dropdown-item" data-value="${programme.id}">${programme.name}</button></li>`
                ).join('');
                programmeToggle.disabled = false;
                programmeDropdown.reset('Select Programme');
            });
    });

    // Real upload progress via XHR - a plain form POST gives no feedback
    // while a large roster file is still going up, which reads as a
    // frozen page. The Download Template button shares this same <form>
    // (via formaction/formmethod overrides) and must keep working as a
    // normal GET download, so only the Upload submit is intercepted.
    const form = document.getElementById('rosterImportForm');
    const uploadBtn = document.getElementById('uploadSubmitBtn');
    const progressWrap = document.getElementById('uploadProgressWrap');
    const progressBar = document.getElementById('uploadProgressBar');
    const progressPercent = document.getElementById('uploadProgressPercent');
    const progressLabel = document.getElementById('uploadProgressLabel');

    form.addEventListener('submit', function (event) {
        const submitter = event.submitter;
        if (submitter && submitter !== uploadBtn) {
            return; // Download Template button - let the normal GET proceed.
        }

        // campus_id/program_id/graduation_year are hidden inputs (the
        // dropdown-menu picker's real value), so the browser's native
        // `required` check never sees them - verify manually before
        // submitting.
        if (!document.getElementById('campus_id').value || !document.getElementById('program_id').value || !document.getElementById('graduation_year').value) {
            event.preventDefault();
            showImportWarning('Please select a Campus, a Programme and a Graduation Year first.');
            return;
        }

        event.preventDefault();

        const formData = new FormData(form);
        uploadBtn.disabled = true;
        form.querySelector('input[name="file"]').value = '';
        progressWrap.classList.remove('d-none');
        progressLabel.textContent = 'Uploading...';

        const csrfToken = form.querySelector('input[name="_token"]').value;

        const resetProgress = function () {
            uploadBtn.disabled = false;
            progressWrap.classList.add('d-none');
            progressBar.style.width = '0%';
            progressPercent.textContent = '0%';
        };

        const showImportWarning = function (message) {
            const warningEl = document.getElementById('importWarning');
            const warningText = document.getElementById('importWarningText');
            if (warningEl && warningText) {
                warningText.textContent = message;
                warningEl.classList.remove('d-none');
                warningEl.scrollIntoView({behavior: 'smooth', block: 'start'});
            }
        };

        const showProgress = function (percent, text) {
            progressBar.style.width = percent + '%';
            progressBar.setAttribute('aria-valuenow', percent);
            progressPercent.textContent = text;
        };

        // Step 2: the file is already validated and parked on the server -
        // import it a few rows at a time, drawing the bar from the counts
        // each call reports back, so 100% really means every row is done.
        const importInChunks = async function (processUrl, total) {
            progressLabel.textContent = 'Importing graduates...';
            showProgress(0, '0 of ' + total + ' · 0%');

            try {
                let finished = false;
                while (!finished) {
                    const response = await fetch(processUrl, {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                    });
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    const result = await response.json();
                    const percent = result.total ? Math.round((result.processed / result.total) * 100) : 100;
                    showProgress(percent, result.processed + ' of ' + result.total + ' · ' + percent + '%');
                    finished = result.done;
                }

                progressLabel.textContent = 'Done - loading results...';
                window.location.reload();
            } catch (error) {
                resetProgress();
                showImportWarning('The import was interrupted. Some graduates may already have been saved - re-upload the same file to finish (already-saved graduates are simply updated).');
            }
        };

        // Step 1: send the file. This request returns JSON, never a
        // redirect - an XHR that follows a redirect loads the destination
        // page itself and uses up the green "uploaded successfully" flash
        // message meant for the reload afterwards.
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route('students.import.stage') }}', true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.addEventListener('progress', function (evt) {
            if (!evt.lengthComputable) {
                return;
            }
            const percent = Math.round((evt.loaded / evt.total) * 100);
            progressLabel.textContent = 'Uploading file...';
            showProgress(percent, percent + '%');
        });

        xhr.addEventListener('load', function () {
            let data = {};
            try {
                data = JSON.parse(xhr.responseText);
            } catch (e) {
                // fall through to the generic failure below
            }

            if (xhr.status >= 200 && xhr.status < 300 && data.process_url) {
                importInChunks(data.process_url, data.total);
                return;
            }

            if (data.error) {
                // Already flashed to the session server-side - reload shows the red banner.
                window.location.reload();
                return;
            }

            resetProgress();
            const firstError = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Upload failed - please try again.');
            showImportWarning(firstError);
        });

        xhr.addEventListener('error', function () {
            resetProgress();
            showImportWarning('Upload failed - please check your connection and try again.');
        });

        xhr.send(formData);
    });

    // History search: filter rows by reference, programme or campus.
    const historySearchInput = document.getElementById('historySearchInput');
    if (historySearchInput) {
        historySearchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            document.querySelectorAll('[data-history-table] tbody tr').forEach(function (row) {
                const text = row.dataset.searchText || '';
                row.style.display = !query || text.includes(query) ? '' : 'none';
            });
        });
    }

    // View batch modal
    const viewBatchModalEl = document.getElementById('viewBatchModal');
    if (viewBatchModalEl) {
        const viewBatchModal = new bootstrap.Modal(viewBatchModalEl);

        document.querySelectorAll('[data-view-batch]').forEach(function (button) {
            button.addEventListener('click', function () {
                const batch = JSON.parse(button.dataset.batch || '{}');

                document.getElementById('viewBatchReference').textContent = batch.reference ?? '';
                document.getElementById('viewBatchType').innerHTML = batch.type === 'download'
                    ? '<span class="badge bg-secondary-subtle text-secondary rounded-sm"><i class="ti ti-download"></i> Download</span>'
                    : '<span class="badge bg-primary-subtle text-primary rounded-sm"><i class="ti ti-upload"></i> Upload</span>';
                document.getElementById('viewBatchWhen').textContent = batch.when ?? '—';
                document.getElementById('viewBatchCampus').textContent = batch.campus ?? '—';
                document.getElementById('viewBatchProgramme').textContent = batch.program_abbreviation ?? batch.program ?? '—';
                document.getElementById('viewBatchYear').textContent = batch.year ?? '—';
                document.getElementById('viewBatchBy').textContent = batch.by ?? '—';
                document.getElementById('viewBatchStatus').innerHTML = batch.status === 'closed'
                    ? '<span class="badge bg-light text-muted border rounded-sm">Closed</span>'
                    : '<span class="badge bg-success-subtle text-success rounded-sm">Open</span>';
                document.getElementById('viewBatchFilename').textContent = batch.original_filename ?? '—';
                document.getElementById('viewBatchNew').textContent = batch.new ?? 0;
                document.getElementById('viewBatchUpdated').textContent = batch.updated ?? 0;

                const reviewBadge = document.getElementById('viewBatchReviewBadge');
                const reviewBtn = document.getElementById('viewBatchReviewBtn');
                if (batch.review > 0) {
                    document.getElementById('viewBatchReview').textContent = batch.review;
                    reviewBadge.classList.remove('d-none');
                    reviewBtn.classList.remove('d-none');
                    reviewBtn.dataset.reference = batch.reference;
                    reviewBtn.dataset.items = JSON.stringify(batch.review_items || []);
                } else {
                    reviewBadge.classList.add('d-none');
                    reviewBtn.classList.add('d-none');
                }

                const incomplete = document.getElementById('viewBatchIncomplete');
                if (batch.is_incomplete) {
                    incomplete.textContent = batch.incomplete_text;
                    incomplete.classList.remove('d-none');
                } else {
                    incomplete.classList.add('d-none');
                }

                const originalProgramEl = document.getElementById('viewBatchOriginalProgram');
                if (batch.original_program) {
                    document.getElementById('viewBatchOriginalProgramName').textContent = batch.original_program;
                    originalProgramEl.classList.remove('d-none');
                } else {
                    originalProgramEl.classList.add('d-none');
                }

                const openLink = document.getElementById('viewBatchOpenStudents');
                openLink.href = batch.actions?.batch_students ?? '#';

                const changeYearBtn = document.getElementById('viewBatchChangeYearBtn');
                const changeProgrammeBtn = document.getElementById('viewBatchChangeProgrammeBtn');
                const closeForm = document.getElementById('viewBatchCloseForm');
                const closeBtn = document.getElementById('viewBatchCloseBtn');

                if (batch.status === 'open' && batch.type !== 'download') {
                    changeYearBtn.classList.remove('d-none');
                    changeProgrammeBtn.classList.remove('d-none');
                    closeForm.classList.remove('d-none');

                    changeYearBtn.dataset.action = batch.actions?.reassign_year;
                    changeYearBtn.dataset.reference = batch.reference;
                    changeYearBtn.dataset.year = batch.year;
                    changeYearBtn.dataset.students = batch.new + batch.updated;

                    changeProgrammeBtn.dataset.action = batch.actions?.reassign_programme;
                    changeProgrammeBtn.dataset.reference = batch.reference;
                    changeProgrammeBtn.dataset.programme = batch.program_abbreviation ?? batch.program ?? '';
                    changeProgrammeBtn.dataset.students = batch.new + batch.updated;

                    closeForm.action = batch.actions?.close;
                    closeBtn.onclick = function () {
                        confirmSubmit(closeBtn, 'Close reference ' + batch.reference + '? This marks the batch as finished - it moves to the Closed tab and can no longer be changed.', {danger: false, title: 'Close Reference', confirmText: 'Close Reference'});
                    };
                } else {
                    changeYearBtn.classList.add('d-none');
                    changeProgrammeBtn.classList.add('d-none');
                    closeForm.classList.add('d-none');
                }

                viewBatchModal.show();
            });
        });
    }
});
</script>
@endsection
