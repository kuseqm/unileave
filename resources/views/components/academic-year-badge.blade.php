@props(['dark' => false])

@php
    $year = \App\Models\AcademicYear::current();
@endphp

@if($year)
    <div class="academic-year-badge {{ $dark ? 'academic-year-badge--dark' : '' }}">
        <i class="ti ti-calendar-stats"></i>
        <span class="fw-semibold">{{ $year->label }}</span>
        <span class="academic-year-badge-sep">&middot;</span>
        <span>{{ $year->current_semester_name }}</span>
        <span class="academic-year-badge-sep">&middot;</span>
        <span class="text-muted">{{ $year->start_date->format('d M') }} - {{ $year->end_date->format('d M Y') }}</span>
    </div>

    <style>
        .academic-year-badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            font-size: 0.68rem; color: #5C5A6E;
            background: #FBF3DD; border: 1px solid #EEDFAF; border-radius: 50px;
            padding: 0.25rem 0.75rem; white-space: nowrap;
        }
        .academic-year-badge i { color: #96771A; font-size: 0.8rem; }
        .academic-year-badge-sep { opacity: 0.5; }
        .academic-year-badge--dark {
            background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.25); color: rgba(255,255,255,0.85);
        }
        .academic-year-badge--dark i { color: #C9A227; }
        .academic-year-badge--dark .text-muted { color: rgba(255,255,255,0.65) !important; }
    </style>
@endif
