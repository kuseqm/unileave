@extends('layouts.master')

@section('title', 'New Verification Letter')

@section('content')
@php
    $label = fn (array $map) => collect($map)->map(fn ($text, $value) => ['value' => $value, 'label' => $text])->values()->all();
    $signatoryOptions = $signatories->map(fn ($s) => ['value' => $s->id, 'label' => $s->name.' — '.$s->title_en])->all();
    $field = 'form-control form-control-sm rounded-sm';
    $small = 'font-size: 0.65rem;';
@endphp
<div class="container-fluid px-2">

    <x-page-header title="NEW VERIFICATION LETTER" description="Confirm that a graduate's certificate and transcript are authentic">
        <a href="{{ route('verification-letters.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> All Letters
        </a>
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    @if($errors->any())
    <x-alert-danger :session="false">Please fix the highlighted fields: {{ $errors->first() }}</x-alert-danger>
    @endif

    <form id="verificationLetterForm" action="{{ route('verification-letters.store') }}" method="POST">
        @csrf
        <div class="row g-2">
            <div class="col-lg-8">

                <div class="card rounded-sm mb-2">
                    <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-file-certificate text-primary me-1"></i>The letter</span></div>
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <x-scrollable-dropdown name="template" id="template" label="Kind" icon="ti-layout" :required="true" :value="old('template', 'standard')" :options="$label($templates)" :searchable="false"
                                    help-text="DataFlow letters carry the extra facts DataFlow asks for." />
                            </div>
                            <div class="col-md-4">
                                <x-scrollable-dropdown name="language" id="language" label="Language" icon="ti-language" :required="true" :value="old('language', 'en')" :options="$label($languages)" :searchable="false" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="{{ $small }}"><i class="ti ti-calendar me-1 text-muted"></i>Letter date <span class="text-danger">*</span></label>
                                <input type="date" name="letter_date" value="{{ old('letter_date', now()->toDateString()) }}" class="{{ $field }} @error('letter_date') is-invalid @enderror" required>
                            </div>
                            <div class="col-md-8">
                                <x-scrollable-dropdown name="signatory_id" id="signatory_id" label="Signed by" icon="ti-signature" :required="true" :value="old('signatory_id', $signatories->first()?->id)" :options="$signatoryOptions" :searchable="false"
                                    help-text="The hard copy is printed and signed by this person; their office name goes on the letterhead and their title closes the letter." />
                                @if($signatories->isEmpty())<div class="text-danger" style="font-size: 0.62rem;">No signatory is set up yet - ask an administrator to add one.</div>@endif
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="{{ $small }}">Subject line <span class="text-muted" data-subject-hint>(optional)</span></label>
                                <input type="text" name="subject" value="{{ old('subject') }}" class="{{ $field }}" placeholder="Leave empty for the standard one">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card rounded-sm mb-2">
                    <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-mail text-primary me-1"></i>Addressed to</span></div>
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label" style="{{ $small }}">Designation</label>
                                <input type="text" name="recipient_title" value="{{ old('recipient_title') }}" class="{{ $field }}" placeholder="e.g. Human Resource Manager">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="{{ $small }}">Organisation <span class="text-danger">*</span></label>
                                <input type="text" name="recipient_organisation" value="{{ old('recipient_organisation') }}" class="{{ $field }} @error('recipient_organisation') is-invalid @enderror" placeholder="e.g. TEF Consult" required>
                                @error('recipient_organisation')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" style="{{ $small }}">Address <span class="text-muted">(one line per row: P.O. Box, building, street)</span></label>
                                <textarea name="recipient_address" rows="2" class="{{ $field }}" placeholder="P. O. BOX 33542, 23rd FLOOR&#10;MILLENIUM TOWER, MAKUMBUSHO">{{ old('recipient_address') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="{{ $small }}">City</label>
                                <input type="text" name="recipient_city" value="{{ old('recipient_city') }}" class="{{ $field }}" placeholder="e.g. Dar es Salaam">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="{{ $small }}">Country <span class="text-muted">(if abroad)</span></label>
                                <input type="text" name="recipient_country" value="{{ old('recipient_country') }}" class="{{ $field }}" placeholder="e.g. Ethiopia">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="{{ $small }}">Email</label>
                                <input type="text" name="recipient_email" value="{{ old('recipient_email') }}" class="{{ $field }}" placeholder="info@example.com">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" style="{{ $small }}">UFS <span class="text-muted">(optional - printed under the address)</span></label>
                                <input type="text" name="ufs" value="{{ old('ufs') }}" class="{{ $field }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card rounded-sm mb-2 d-none" data-custom-only>
                    <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-pencil text-primary me-1"></i>The whole letter</span></div>
                    <div class="card-body p-3">
                        <label class="form-label" style="font-size: 0.65rem;">Letter text <span class="text-danger">*</span> <span class="text-muted">(a new line starts a new paragraph - the first is the opening one, the rest are numbered from 2)</span></label>
                        <textarea name="body_text" rows="12" class="form-control form-control-sm rounded-sm @error('body_text') is-invalid @enderror" placeholder="This refers to your letter dated ...">{{ old('body_text') }}</textarea>
                        @error('body_text')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                        <small class="text-muted" style="font-size: 0.6rem;">The subject line above is required for this kind. The reference number, date, address, signature and letterhead are added as usual.</small>
                    </div>
                </div>

                <div class="card rounded-sm mb-2" data-standard-only>
                    <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-inbox text-primary me-1"></i>Their request</span></div>
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <x-scrollable-dropdown name="received_via" id="received_via" label="It came as" icon="ti-arrow-down-left" :required="true" :value="old('received_via', 'letter')" :options="$label($receivedVia)" :searchable="false" />
                            </div>
                            <div class="col-md-5">
                                <label class="form-label" style="{{ $small }}">Their letter's reference number</label>
                                <input type="text" name="received_reference" value="{{ old('received_reference') }}" class="{{ $field }}" placeholder="e.g. TEF/ACC VER/2026/003">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="{{ $small }}">Their date</label>
                                <input type="date" name="received_date" value="{{ old('received_date') }}" class="{{ $field }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="{{ $small }}"><i class="ti ti-files me-1 text-muted"></i>What they asked to verify <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3 pt-1" style="font-size: 0.75rem;">
                                    @php($documents = old('documents', ['certificate']))
                                    <label class="mb-0"><input type="checkbox" name="documents[]" value="certificate" @checked(in_array('certificate', $documents))> Certificate</label>
                                    <label class="mb-0"><input type="checkbox" name="documents[]" value="transcript" @checked(in_array('transcript', $documents))> Transcript</label>
                                </div>
                                @error('documents')<div class="text-danger" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <x-scrollable-dropdown name="number_type" id="number_type" label="Show in the table" icon="ti-hash" :required="true" :value="old('number_type', 'certificate')" :options="$label($numberTypes)" :searchable="false" />
                            </div>
                            <div class="col-md-6 d-none" data-dataflow-only>
                                <label class="form-label" style="{{ $small }}">Mode of study</label>
                                <input type="text" name="mode_of_study" value="{{ old('mode_of_study', 'Full Time') }}" class="{{ $field }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card rounded-sm mb-2" data-standard-only>
                    <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-school text-primary me-1"></i>Graduates</span></div>
                    <div class="card-body p-3">
                        <div class="position-relative mb-2">
                            <input type="search" id="graduateSearch" class="{{ $field }}" placeholder="Search a graduate by name, registration number or certificate number, then pick them..." autocomplete="off">
                            <div id="graduateResults" class="list-group position-absolute w-100 shadow-sm d-none" style="z-index: 20; max-height: 240px; overflow-y: auto; font-size: 0.72rem;"></div>
                        </div>
                        @error('items')<div class="text-danger mb-1" style="font-size: 0.65rem;">{{ $message }}</div>@enderror
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 align-middle" style="font-size: 0.7rem;" id="graduateTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th><th>Number</th><th>Programme / level</th><th style="width: 80px;">Year</th>
                                        <th data-dataflow-only class="d-none">Barcode</th><th data-dataflow-only class="d-none">From</th><th data-dataflow-only class="d-none">To</th><th data-dataflow-only class="d-none" style="width: 60px;">Yrs</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="graduateRows"></tbody>
                            </table>
                        </div>
                        <div id="noGraduates" class="text-muted text-center py-3" style="font-size: 0.7rem;">No graduates added yet. For a Swahili letter, type the programme in Swahili in the table (e.g. Shahada ya awali ya Uuguzi).</div>
                    </div>
                </div>

                <div class="card rounded-sm mb-2" data-standard-only>
                    <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-text-plus text-primary me-1"></i>Extra paragraph <span class="text-muted fw-normal">(optional)</span></span></div>
                    <div class="card-body p-3">
                        <textarea name="extra_text" rows="3" class="form-control form-control-sm rounded-sm" placeholder="Only if something more must be said. It is printed as its own numbered paragraph right after the table, before the closing lines.">{{ old('extra_text') }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-1 mb-3">
                    <button type="submit" formaction="{{ route('verification-letters.preview') }}" formtarget="_blank" class="btn btn-outline-primary btn-sm rounded-sm" style="font-size: 0.72rem;"><i class="ti ti-eye me-1"></i> Preview</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.72rem;"><i class="ti ti-file-check me-1"></i> Generate Letter</button>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card rounded-sm mb-2">
                    <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-hash text-primary me-1"></i>Reference number</span></div>
                    <div class="card-body p-2" style="font-size: 0.72rem;">
                        @if($format)
                            <div class="text-muted" style="font-size: 0.62rem;">The next letter will be</div>
                            <div class="fw-semibold"><code style="font-size: 0.78rem;">{{ $format->peekNextForCurrentVolume() }}</code></div>
                            <small class="text-muted" style="font-size: 0.62rem;">From the Registry's format "{{ $format->name }}". A volume holds {{ $format->file_capacity ?: 'any number of' }} letters, then the next volume starts by itself.</small>
                        @else
                            <div class="text-danger"><i class="ti ti-alert-triangle me-1"></i> No reference format exists for these letters yet.</div>
                            <small class="text-muted">Ask the Registry to add one under Reference Formats (kind: Certificate / Transcript Verification Letter).</small>
                        @endif
                    </div>
                </div>
                <x-alert-light class="mb-0 py-2 px-2 rounded-sm">
                    <small class="text-muted" style="font-size: 0.62rem;">
                        <strong class="d-block" style="font-size: 0.65rem;">How it works</strong>
                        Fill in who asked, pick the graduates, choose who signs, then use <strong>Preview</strong> to read the letter before <strong>Generate</strong> takes a reference number. Names, numbers and programmes come from the graduate's record, and you can correct any of them in the table.
                    </small>
                </x-alert-light>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchUrl = @json(route('verification-letters.graduates'));
    const rows = document.getElementById('graduateRows');
    const emptyNote = document.getElementById('noGraduates');
    const search = document.getElementById('graduateSearch');
    const results = document.getElementById('graduateResults');
    const templateInput = document.getElementById('template');
    const languageInput = document.getElementById('language');
    const languageToggle = document.getElementById('language_toggle');
    let counter = 0;
    let timer;

    const isDataflow = () => templateInput.value === 'dataflow';

    function applyTemplate() {
        const custom = templateInput.value === 'custom';
        document.querySelectorAll('[data-custom-only]').forEach(el => el.classList.toggle('d-none', !custom));
        document.querySelectorAll('[data-standard-only]').forEach(el => el.classList.toggle('d-none', custom));
        document.querySelector('[data-subject-hint]').textContent = custom ? '(required)' : '(optional)';
        document.querySelector('[name="subject"]').required = custom;
        document.querySelector('[name="body_text"]').required = custom;
        document.querySelectorAll('[data-dataflow-only]').forEach(el => el.classList.toggle('d-none', !isDataflow()));
        if (isDataflow()) {
            languageInput.value = 'en';
            languageToggle.textContent = 'English';
            languageToggle.disabled = true;
        } else {
            languageToggle.disabled = false;
        }
    }

    function cell(name, value, extra = {}) {
        const td = document.createElement('td');
        if (extra.dataflow) { td.dataset.dataflowOnly = ''; if (!isDataflow()) td.classList.add('d-none'); }
        const input = document.createElement('input');
        input.type = extra.type || 'text';
        input.name = `items[${extra.index}][${name}]`;
        input.value = value ?? '';
        input.className = 'form-control form-control-sm rounded-sm';
        input.style.fontSize = '0.68rem';
        if (extra.required) input.required = true;
        td.appendChild(input);
        return td;
    }

    function addRow(item) {
        const index = counter++;
        const tr = document.createElement('tr');
        const hidden = (name, value) => { const i = document.createElement('input'); i.type = 'hidden'; i.name = `items[${index}][${name}]`; i.value = value ?? ''; return i; };

        const nameCell = cell('name', item.name, {index, required: true});
        nameCell.appendChild(hidden('student_id', item.student_id));
        nameCell.appendChild(hidden('gender', item.gender));
        tr.appendChild(nameCell);
        tr.appendChild(cell('number', item.number, {index, required: true}));
        tr.appendChild(cell('programme', item.programme, {index, required: true}));
        tr.appendChild(cell('graduation_year', item.graduation_year, {index, required: true}));
        tr.appendChild(cell('barcode', item.barcode, {index, dataflow: true}));
        tr.appendChild(cell('study_from', item.study_from, {index, dataflow: true}));
        tr.appendChild(cell('study_to', item.study_to, {index, dataflow: true}));
        tr.appendChild(cell('duration_years', item.duration_years, {index, dataflow: true, type: 'number'}));

        const remove = document.createElement('td');
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-outline-danger btn-sm rounded-sm';
        button.style.cssText = 'font-size: 0.6rem; padding: 0.05rem 0.35rem;';
        button.textContent = '×';
        button.title = 'Remove';
        button.addEventListener('click', () => { tr.remove(); refreshEmptyNote(); });
        remove.appendChild(button);
        tr.appendChild(remove);

        rows.appendChild(tr);
        refreshEmptyNote();
    }

    function refreshEmptyNote() {
        emptyNote.classList.toggle('d-none', rows.children.length > 0);
    }

    function chooseGraduate(graduate) {
        const numberType = document.getElementById('number_type').value;
        addRow({
            student_id: graduate.student_id,
            gender: graduate.gender,
            name: graduate.name,
            number: numberType === 'registration' ? graduate.registration_number : graduate.certificate_number,
            programme: graduate.programme,
            graduation_year: graduate.graduation_year,
            study_from: graduate.study_from,
            study_to: graduate.study_to,
            duration_years: graduate.duration_years,
        });
        search.value = '';
        results.classList.add('d-none');
        window.lastGraduates = window.lastGraduates || {};
        window.lastGraduates[graduate.student_id] = graduate;
    }

    search.addEventListener('input', function () {
        clearTimeout(timer);
        const term = search.value.trim();
        if (term.length < 2) { results.classList.add('d-none'); return; }
        timer = setTimeout(async () => {
            const response = await fetch(searchUrl + '?q=' + encodeURIComponent(term), {headers: {'Accept': 'application/json'}});
            if (!response.ok) return;
            const found = await response.json();
            results.replaceChildren();
            found.forEach(graduate => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'list-group-item list-group-item-action';
                button.textContent = graduate.label + ' — ' + graduate.programme;
                button.addEventListener('click', () => chooseGraduate(graduate));
                results.appendChild(button);
            });
            if (!found.length) { const none = document.createElement('div'); none.className = 'list-group-item text-muted'; none.textContent = 'No graduate matches.'; results.appendChild(none); }
            results.classList.remove('d-none');
        }, 250);
    });

    document.addEventListener('click', event => { if (!results.contains(event.target) && event.target !== search) results.classList.add('d-none'); });
    document.getElementById('template_menu').addEventListener('click', () => setTimeout(applyTemplate, 0));

    // Rebuild the rows after a validation error, so nothing typed is lost.
    @json(old('items', [])).forEach(addRow);

    // Opened from a graduate's record: that graduate is the first row.
    const prefill = @json($prefill);
    if (prefill && rows.children.length === 0) {
        chooseGraduate(prefill);
    }
    applyTemplate();
    refreshEmptyNote();
});
</script>
@endsection
