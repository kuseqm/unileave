@extends('layouts.master')

@section('title', $student->formatted_name)

@section('content')
@php
    $initials = mb_strtoupper(mb_substr($student->first_name ?? '', 0, 1).mb_substr($student->last_name ?? '', 0, 1));
    $genderLabel = ['M' => 'Male', 'F' => 'Female'][$student->gender] ?? null;

    $missing = array_filter([
        $student->classification ? null : 'Classification',
        $student->gpa ? null : 'GPA',
    ]);

    $programmeDetails = [
        ['icon' => 'ti-book', 'label' => 'Programme', 'value' => $student->program->name ?? null],
        ['icon' => 'ti-building-arch', 'label' => 'Faculty / School', 'value' => $student->program->school->name ?? null],
        ['icon' => 'ti-certificate', 'label' => 'Degree', 'value' => $student->degree_with_abbreviation],
        ['icon' => 'ti-map-pin', 'label' => 'Campus', 'value' => $student->campus->name ?? null],
        ['icon' => 'ti-calendar-event', 'label' => 'Study Period', 'value' => $student->programme_start_academic_year ? $student->programme_start_academic_year.' to '.$student->programme_end_academic_year : null],
        ['icon' => 'ti-hourglass', 'label' => 'Duration', 'value' => $student->programme_duration_years ? $student->programme_duration_years.' '.($student->programme_duration_years == 1 ? 'year' : 'years') : null],
    ];

    $bookCover = $student->graduation_year
        ? \App\Models\GraduationCeremony::where('year', $student->graduation_year)->whereNotNull('book_cover_path')->first()
        : null;

    $graduationDetails = [
        ['icon' => 'ti-calendar-check', 'label' => 'Graduation Date', 'value' => optional($student->graduation_date)->format('d M Y')],
        ['icon' => 'ti-award', 'label' => 'Ceremony', 'value' => $student->ordinal_graduation_ceremony, 'cover' => $bookCover ?? null],
        ['icon' => 'ti-barcode', 'label' => 'Certificate Number', 'value' => $student->certificate_barcode],
        ['icon' => 'ti-gender-bigender', 'label' => 'Gender', 'value' => $genderLabel],
    ];
@endphp
<div class="container-fluid px-2">

    <x-page-header title="{{ $student->is_graduated ? 'GRADUATE RECORD' : 'STUDENT RECORD' }}" description="{{ $student->registration_number }}">
        @can('import_students')
        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-edit me-1"></i> Edit
        </a>
        @endcan
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Graduates
        </a>
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2">
        <div class="col-lg-8">

            <!-- Identity -->
            <div class="card rounded-sm mb-2">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 fw-bold text-primary"
                             style="width: 3.4rem; height: 3.4rem; border-radius: 50%; background: #EAF1FB; border: 2px solid #A3C4E8; font-size: 1.05rem; letter-spacing: 0.03em;">
                            {{ $initials }}
                        </div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="fw-bold" style="font-size: 1rem; line-height: 1.25;">{{ $student->formatted_name }}</div>
                            <div class="mt-1 d-flex flex-wrap align-items-center gap-2" style="font-size: 0.7rem;">
                                <span class="text-muted">Reg. No.</span> <code>{{ $student->registration_number }}</code>
                                @if($student->program?->code)
                                    <span class="badge bg-primary-subtle text-primary rounded-sm">{{ $student->program->code }}</span>
                                @endif
                                @if($student->campus)
                                    <span class="text-muted"><i class="ti ti-map-pin"></i> {{ $student->campus->name }}</span>
                                @endif
                            </div>
                        </div>
                        @if($student->is_graduated)
                            <span class="badge bg-success rounded-sm align-self-start" style="font-size: 0.65rem;"><i class="ti ti-check"></i> Graduated</span>
                        @else
                            <span class="badge bg-secondary rounded-sm align-self-start" style="font-size: 0.65rem;">Not Graduated</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Key figures -->
            <div class="row g-2 mb-2">
                <x-stats-card :value="$student->graduation_year ?? '—'" label="Graduation Year" color="primary" icon="school" />
                <x-stats-card :value="$student->programme_duration_years ? $student->programme_duration_years.' yrs' : '—'" label="Programme Length" color="info" icon="hourglass" />
                <x-stats-card :value="$student->classification ?? '—'" label="Classification" color="success" icon="medal" />
                <x-stats-card :value="$student->gpa ?? '—'" label="GPA" color="warning" icon="chart-line" />
            </div>

            @if($missing)
            <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                <div class="d-flex align-items-start gap-1">
                    <i class="ti ti-alert-triangle text-warning mt-0" style="font-size: 0.7rem;"></i>
                    <div>
                        <strong class="d-block" style="font-size: 0.65rem;">Some details are still missing</strong>
                        <small class="text-muted" style="font-size: 0.6rem;">
                            {{ implode(', ', $missing) }} - letters will render with gaps until these are filled in
                            @can('import_students')via <a href="{{ route('students.edit', $student->id) }}">Edit</a>@else by the Examinations Office @endcan.
                        </small>
                    </div>
                </div>
            </x-alert-light>
            @endif

            <!-- Details -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center">
                    <i class="ti ti-id text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Record Details</span>
                </div>
                <div class="card-body p-3">
                    @foreach([['Programme & Degree', $programmeDetails], ['Graduation', $graduationDetails]] as [$sectionTitle, $items])
                        <div class="text-uppercase text-muted fw-semibold mb-2 {{ $loop->first ? '' : 'mt-3 pt-3 border-top' }}" style="font-size: 0.6rem; letter-spacing: 0.06em;">{{ $sectionTitle }}</div>
                        <div class="row g-3">
                            @foreach($items as $item)
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="ti {{ $item['icon'] }} text-primary mt-1" style="font-size: 0.85rem;"></i>
                                    <div style="min-width: 0;">
                                        <div class="text-muted" style="font-size: 0.62rem;">{{ $item['label'] }}</div>
                                        @if($item['value'])
                                            <div class="fw-semibold" style="font-size: 0.75rem;">{{ $item['value'] }}</div>
                                        @else
                                            <div class="text-muted" style="font-size: 0.75rem;">Not recorded</div>
                                        @endif
                                        @if(! empty($item['cover']))
                                        <a href="{{ route('students.book-cover', $item['cover']->id) }}" target="_blank" class="d-inline-flex align-items-center gap-2 mt-1 text-decoration-none" title="Open the full graduation book cover">
                                            <img src="{{ route('students.book-cover', $item['cover']->id) }}" alt="{{ $item['cover']->year }} graduation book" style="width: 44px; height: 58px; object-fit: cover; border-radius: 3px; border: 1px solid #ddd;">
                                            <span class="text-muted" style="font-size: 0.6rem;">{{ $item['cover']->year }} graduation book</span>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Previously issued letters -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-history me-1"></i> Letters Issued</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">Letter Number</th>
                                    <th class="py-1">Issued</th>
                                    <th class="py-1">Verified</th>
                                    <th class="pe-2 py-1 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($student->graduationLetters as $letter)
                                <tr>
                                    <td class="ps-2 py-1"><code style="font-size: 0.65rem;">{{ $letter->letter_number }}</code></td>
                                    <td class="py-1">{{ $letter->created_at->format('d M Y, H:i') }}</td>
                                    <td class="py-1">
                                        @if($letter->verified_at)
                                            <span class="badge bg-info rounded-sm" style="font-size: 0.6rem;">{{ $letter->verified_at->diffForHumans() }}</span>
                                        @else
                                            <span class="text-muted" style="font-size: 0.65rem;">Not yet scanned</span>
                                        @endif
                                    </td>
                                    <td class="pe-2 py-1 text-end">
                                        <a href="{{ route('graduation-letters.show', $letter->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No letters issued yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Letters -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light d-flex align-items-center">
                    <i class="ti ti-file-certificate text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Letters</span>
                </div>
                @if($student->is_graduated)
                <div class="list-group list-group-flush">
                    @can('prepare_verification_letters')
                    <a href="{{ route('verification-letters.create', ['student' => $student->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 px-3 py-2">
                        <i class="ti ti-file-check text-success" style="font-size: 1.1rem;"></i>
                        <span>
                            <span class="d-block fw-semibold" style="font-size: 0.72rem;">Verification Letter</span>
                            <span class="d-block text-muted" style="font-size: 0.62rem;">Confirms the certificate and transcript are authentic</span>
                        </span>
                        <i class="ti ti-chevron-right ms-auto text-muted"></i>
                    </a>
                    @endcan
                    @foreach([
                        ['introduction', 'Introduction Letter', 'Introduce the graduate to a third party', 'ti-user-share'],
                        ['english_proficiency', 'English Proficiency Letter', 'Certify English as the medium of instruction', 'ti-language'],
                        ['recommendation', 'Recommendation Letter', 'Uses classification and GPA when available', 'ti-thumb-up'],
                    ] as [$letterType, $letterLabel, $letterHint, $letterIcon])
                    <a href="{{ route('students.compose-letter', ['student' => $student->id, 'type' => $letterType]) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 px-3 py-2">
                        <i class="ti {{ $letterIcon }} text-primary" style="font-size: 1.1rem;"></i>
                        <span>
                            <span class="d-block fw-semibold" style="font-size: 0.72rem;">{{ $letterLabel }}</span>
                            <span class="d-block text-muted" style="font-size: 0.62rem;">{{ $letterHint }}</span>
                        </span>
                        <i class="ti ti-chevron-right ms-auto text-muted"></i>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="card-body p-3 text-muted" style="font-size: 0.7rem;">Letters can only be issued once the record is marked as graduated.</div>
                @endif
            </div>

            <x-alert-light class="mb-0 py-2 px-2 rounded-sm">
                <div class="d-flex align-items-start gap-1">
                    <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                    <div>
                        <strong class="d-block" style="font-size: 0.65rem;">Reference Numbers</strong>
                        <small class="text-muted" style="font-size: 0.6rem;">Letter numbers come from Registry's Reference Formats (document type "Graduation Confirmation Letter"). If none is configured yet, a fallback format is used automatically.</small>
                    </div>
                </div>
            </x-alert-light>
        </div>
    </div>
</div>
@endsection
