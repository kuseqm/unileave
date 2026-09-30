@extends('layouts.master')

@section('title', 'Manage Academic Catalog')

@section('content')
<div class="container-fluid px-2">

    <x-page-header title="ACADEMIC CATALOG" description="Add the Faculty/School, Programme and Graduation Ceremony entries the Graduation Roster import draws from">
        <a href="{{ route('students.import.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Import
        </a>
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $schools->count() }}" label="Faculties / Schools" color="primary" icon="building-arch" />
        <x-stats-card value="{{ $programs->count() }}" label="Programmes" color="success" icon="book" />
        <x-stats-card value="{{ $graduationCeremonies->count() }}" label="Graduation Ceremonies" color="info" icon="calendar-event" />
        <x-stats-card value="{{ $campuses->count() }}" label="Campuses" color="warning" icon="map-pin" />
    </div>

    {{-- Faculty / School --}}
    <div class="row g-2 mb-3">
        <div class="col-lg-4">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center">
                    <i class="ti ti-building-arch text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Add Faculty / School</span>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('students.catalog.schools.store') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm rounded-sm @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. School of Engineering" required>
                            @error('name')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <x-scrollable-dropdown
                                    name="type"
                                    id="new_school_type"
                                    label="Type"
                                    icon="ti-category"
                                    :required="true"
                                    placeholder="Select Type"
                                    :options="collect($schoolTypes)->map(fn ($type) => ['value' => $type, 'label' => $type])->all()"
                                />
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size: 0.65rem;">Abbreviation <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control form-control-sm rounded-sm @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="e.g. SOE" required>
                                @error('code')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <x-scrollable-dropdown
                                name="campus_id"
                                id="new_school_campus_id"
                                label="Campus"
                                icon="ti-map-pin"
                                :required="true"
                                placeholder="Select Campus"
                                :options="$campuses->map(fn ($campus) => ['value' => $campus->id, 'label' => $campus->name])->all()"
                            />
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm rounded-sm">
                            <i class="ti ti-plus me-1"></i> Add Faculty/School
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between">
                    <span style="font-size: 0.7rem; font-weight: 600;">Faculties / Schools on File</span>
                    <span class="text-muted" style="font-size: 0.6rem;">{{ $schools->count() }} total</span>
                </div>
                <div class="p-2 border-bottom">
                    <div class="position-relative">
                        <i class="ti ti-search position-absolute text-muted" style="left: 0.6rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem;"></i>
                        <input type="text" id="schoolSearchInput" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding-left: 1.8rem;" placeholder="Search by name, code or campus..." autocomplete="off">
                    </div>
                </div>
                @if($schools->isNotEmpty())
                <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                    <table id="schoolsTable" class="table table-sm mb-0" style="font-size: 0.7rem;">
                        <thead class="table-light" style="position: sticky; top: 0;">
                            <tr><th>Type</th><th>Code</th><th>Name</th><th>Campus</th></tr>
                        </thead>
                        <tbody>
                            @foreach($schools as $school)
                            <tr data-search-text="{{ strtolower($school->name.' '.$school->code.' '.($school->campus->name ?? '')) }}">
                                <td><span class="badge bg-secondary-subtle text-secondary rounded-sm">{{ $school->type ?? '—' }}</span></td>
                                <td><code>{{ $school->code }}</code></td>
                                <td>{{ $school->name }}</td>
                                <td>{{ $school->campus->name ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4" style="font-size: 0.7rem;">No Faculty/School on file yet.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Programme --}}
    <div class="row g-2 mb-3">
        <div class="col-lg-4">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center">
                    <i class="ti ti-book text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Add Programme</span>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('students.catalog.programs.store') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm rounded-sm @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Bachelor of Science in Statistics" required>
                            @error('name')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label" style="font-size: 0.65rem;">Code</label>
                                <input type="text" name="code" class="form-control form-control-sm rounded-sm @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="e.g. BSTAT">
                                @error('code')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size: 0.65rem;">Level <span class="text-danger">*</span></label>
                                <input type="text" name="study_level" list="studyLevelOptions" class="form-control form-control-sm rounded-sm @error('study_level') is-invalid @enderror" value="{{ old('study_level') }}" placeholder="e.g. Bachelor" required>
                                <datalist id="studyLevelOptions">
                                    <option value="NTA Level 4"></option>
                                    <option value="NTA Level 5"></option>
                                    <option value="NTA Level 6"></option>
                                    <option value="Bachelor"></option>
                                    <option value="Masters"></option>
                                    <option value="PhD"></option>
                                </datalist>
                                @error('study_level')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-2">
                            <x-scrollable-dropdown
                                name="duration_years"
                                id="new_program_duration_years"
                                label="Duration"
                                icon="ti-clock"
                                :required="true"
                                placeholder="Select duration"
                                :value="old('duration_years')"
                                helpText="Normal length of the programme - used to estimate a graduate's start year from their graduation year."
                                :options="collect(range(1, 6))->map(fn ($years) => ['value' => $years, 'label' => $years.' '.($years === 1 ? 'year' : 'years').' ('.($years * 2).' semesters)'])->all()"
                            />
                        </div>
                        <div class="mb-3">
                            <x-scrollable-dropdown
                                name="school_id"
                                id="new_program_school_id"
                                label="Faculty/School"
                                icon="ti-building-arch"
                                :required="true"
                                placeholder="Select Faculty/School"
                                :options="$schools->map(fn ($school) => ['value' => $school->id, 'label' => $school->code.' - '.$school->name])->all()"
                            />
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm rounded-sm">
                            <i class="ti ti-plus me-1"></i> Add Programme
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between">
                    <span style="font-size: 0.7rem; font-weight: 600;">Programmes on File</span>
                    <span class="text-muted" style="font-size: 0.6rem;">{{ $programs->count() }} total</span>
                </div>
                <div class="p-2 border-bottom">
                    <div class="position-relative">
                        <i class="ti ti-search position-absolute text-muted" style="left: 0.6rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem;"></i>
                        <input type="text" id="programSearchInput" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding-left: 1.8rem;" placeholder="Search by name, code or faculty..." autocomplete="off">
                    </div>
                </div>
                @if($programs->isNotEmpty())
                <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                    <table id="programsTable" class="table table-sm mb-0" style="font-size: 0.7rem;">
                        <thead class="table-light" style="position: sticky; top: 0;">
                            <tr><th>Code</th><th>Name</th><th>Faculty</th><th>Years</th><th></th></tr>
                        </thead>
                        <tbody>
                            @foreach($programs as $program)
                            <tr data-search-text="{{ strtolower($program->name.' '.$program->code.' '.($program->school->code ?? '').' '.($program->school->name ?? '')) }}">
                                <td><code>{{ $program->code }}</code></td>
                                <td>{{ $program->name }}</td>
                                <td>{{ $program->school->code ?? '—' }}</td>
                                <td>
                                    <form action="{{ route('students.catalog.programs.duration.update', $program->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="duration_years" class="form-select form-select-sm rounded-sm py-0" style="font-size: 0.65rem; min-width: 3.2rem;" onchange="this.form.submit()" aria-label="Duration in years">
                                            @if(! $program->duration_years)<option value="" selected disabled>—</option>@endif
                                            @foreach(range(1, 6) as $years)
                                                <option value="{{ $years }}" @selected((int) $program->duration_years === $years)>{{ $years }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.05rem 0.4rem;"
                                        data-edit-programme
                                        data-action="{{ route('students.catalog.programs.update', $program->id) }}"
                                        data-name="{{ $program->name }}"
                                        data-code="{{ $program->code }}"
                                        data-level="{{ $program->study_level }}"
                                        data-duration="{{ $program->duration_years }}"
                                        data-school="{{ $program->school_id }}"
                                        data-students="{{ $program->students()->count() }}">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4" style="font-size: 0.7rem;">No Programme on file yet.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Graduation Ceremony --}}
    <div class="row g-2 mb-3">
        <div class="col-lg-4">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center">
                    <i class="ti ti-calendar-event text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Add Graduation Ceremony</span>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('students.catalog.graduation-ceremonies.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label" style="font-size: 0.65rem;">Year <span class="text-danger">*</span></label>
                                <input type="number" name="year" class="form-control form-control-sm rounded-sm @error('year') is-invalid @enderror" value="{{ old('year') }}" min="1900" max="2100" placeholder="e.g. 2026" required>
                                @error('year')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size: 0.65rem;">Ceremony No. <span class="text-danger">*</span></label>
                                <input type="number" name="sequence_number" class="form-control form-control-sm rounded-sm @error('sequence_number') is-invalid @enderror" value="{{ old('sequence_number') }}" min="1" placeholder="e.g. 17" required>
                                @error('sequence_number')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Ceremony Date <span class="text-danger">*</span></label>
                            <input type="date" name="ceremony_date" class="form-control form-control-sm rounded-sm @error('ceremony_date') is-invalid @enderror" value="{{ old('ceremony_date') }}" required>
                            @error('ceremony_date')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size: 0.65rem;"><i class="ti ti-photo me-1 text-muted"></i>Book Cover <span class="text-muted">(optional photo, up to 4 MB)</span></label>
                            <input type="file" name="book_cover" accept="image/jpeg,image/png,image/webp" class="form-control form-control-sm rounded-sm @error('book_cover') is-invalid @enderror">
                            @error('book_cover')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm rounded-sm">
                            <i class="ti ti-plus me-1"></i> Add Ceremony
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card rounded-sm h-100">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center justify-content-between">
                    <span style="font-size: 0.7rem; font-weight: 600;">Graduation Ceremonies on File</span>
                    <span class="text-muted" style="font-size: 0.6rem;">{{ $graduationCeremonies->count() }} total</span>
                </div>
                <div class="p-2 border-bottom">
                    <div class="position-relative">
                        <i class="ti ti-search position-absolute text-muted" style="left: 0.6rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem;"></i>
                        <input type="text" id="ceremonySearchInput" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding-left: 1.8rem;" placeholder="Search by year or ceremony number..." autocomplete="off">
                    </div>
                </div>
                @if($graduationCeremonies->isNotEmpty())
                <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                    <table id="ceremoniesTable" class="table table-sm mb-0" style="font-size: 0.7rem;">
                        <thead class="table-light" style="position: sticky; top: 0;">
                            <tr><th>Year</th><th>No.</th><th>Date</th><th>Book Cover</th></tr>
                        </thead>
                        <tbody>
                            @foreach($graduationCeremonies as $ceremony)
                            <tr data-search-text="{{ strtolower($ceremony->year.' '.$ceremony->sequence_number) }}">
                                <td>{{ $ceremony->year }}</td>
                                <td>{{ $ceremony->sequence_number }}</td>
                                <td>{{ optional($ceremony->ceremony_date)->format('d M Y') ?? '—' }}</td>
                                <td>
                                    <form action="{{ route('students.catalog.graduation-ceremonies.book-cover.update', $ceremony->id) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-1">
                                        @csrf
                                        @if($ceremony->book_cover_path)
                                        <a href="{{ route('students.book-cover', $ceremony->id) }}" target="_blank" title="Open the {{ $ceremony->year }} book cover">
                                            <img src="{{ route('students.book-cover', $ceremony->id) }}" alt="{{ $ceremony->year }} book cover" style="width: 26px; height: 34px; object-fit: cover; border-radius: 2px; border: 1px solid #ddd;">
                                        </a>
                                        @endif
                                        <label class="btn btn-outline-secondary btn-sm rounded-sm mb-0" style="font-size: 0.58rem; padding: 0.05rem 0.35rem; cursor: pointer;">
                                            {{ $ceremony->book_cover_path ? 'Replace' : 'Upload' }}
                                            <input type="file" name="book_cover" accept="image/jpeg,image/png,image/webp" class="d-none" onchange="this.form.submit()">
                                        </label>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4" style="font-size: 0.7rem;">No Graduation Ceremony on file yet.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editProgrammeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="editProgrammeForm" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header py-2 px-3">
                <h6 class="modal-title" style="font-size: 0.8rem;"><i class="ti ti-edit me-1"></i> Edit Programme</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <p class="text-muted mb-2" style="font-size: 0.65rem;" id="editProgrammeNote"></p>
                <div class="mb-2">
                    <label class="form-label" style="font-size: 0.65rem;">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="edit_program_name" class="form-control form-control-sm rounded-sm" required>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label" style="font-size: 0.65rem;">Code</label>
                        <input type="text" name="code" id="edit_program_code" class="form-control form-control-sm rounded-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size: 0.65rem;">Level <span class="text-danger">*</span></label>
                        <input type="text" name="study_level" id="edit_program_level" list="studyLevelOptions" class="form-control form-control-sm rounded-sm" required>
                    </div>
                </div>
                <div class="mb-2">
                    <x-scrollable-dropdown
                        name="duration_years"
                        id="edit_program_duration"
                        label="Duration"
                        icon="ti-clock"
                        :required="true"
                        placeholder="Select duration"
                        :searchable="false"
                        :options="collect(range(1, 6))->map(fn ($years) => ['value' => $years, 'label' => $years.' '.($years === 1 ? 'year' : 'years').' ('.($years * 2).' semesters)'])->all()"
                    />
                </div>
                <x-scrollable-dropdown
                    name="school_id"
                    id="edit_program_school_id"
                    label="Faculty/School"
                    icon="ti-building-arch"
                    :required="true"
                    placeholder="Select Faculty/School"
                    :options="$schools->map(fn ($school) => ['value' => $school->id, 'label' => $school->code.' - '.$school->name])->all()"
                />
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;"><i class="ti ti-check me-1"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = new bootstrap.Modal(document.getElementById('editProgrammeModal'));

    function setDropdown(id, value) {
        const item = document.querySelector('#' + id + '_menu .dropdown-item[data-value="' + value + '"]');
        document.getElementById(id).value = value ?? '';
        document.getElementById(id + '_toggle').textContent = item ? item.textContent.trim() : 'Select';
    }

    document.querySelectorAll('[data-edit-programme]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('editProgrammeForm').action = button.dataset.action;
            document.getElementById('edit_program_name').value = button.dataset.name;
            document.getElementById('edit_program_code').value = button.dataset.code;
            document.getElementById('edit_program_level').value = button.dataset.level;
            setDropdown('edit_program_duration', button.dataset.duration);
            setDropdown('edit_program_school_id', button.dataset.school);
            document.getElementById('editProgrammeNote').textContent = button.dataset.students + ' graduate(s) belong to this programme; if you move it to another Faculty/School they move with it.';
            modal.show();
        });
    });

    // Plain client-side row filter, same technique as the Import History
    // search - these lists are small enough that a server round-trip
    // would just add latency for no benefit.
    function wireTableFilter(inputId, tableId) {
        const input = document.getElementById(inputId);
        const table = document.getElementById(tableId);
        if (! input || ! table) {
            return;
        }
        input.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            table.querySelectorAll('tbody tr').forEach(function (row) {
                const text = row.dataset.searchText || '';
                row.style.display = !query || text.includes(query) ? '' : 'none';
            });
        });
    }

    wireTableFilter('schoolSearchInput', 'schoolsTable');
    wireTableFilter('programSearchInput', 'programsTable');
    wireTableFilter('ceremonySearchInput', 'ceremoniesTable');
});
</script>

@endsection
