@extends('layouts.master')

@section('title', 'New Academic Year')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Academic Years', 'url' => route('admin.academic-years.index')],
        ['name' => 'New Academic Year', 'url' => route('admin.academic-years.create')]
    ]" />

    <x-page-header title="NEW ACADEMIC YEAR" description="Dates below are suggested from the last academic year on file - review and adjust before saving. This will not take effect until you activate it from the list." />

    <x-alert-danger />

    <div class="card border-0 rounded-sm shadow-sm">
        <div class="card-body p-2">
            <form method="POST" action="{{ route('admin.academic-years.store') }}">
                @csrf
                @php
                    $academicYear = (object) [
                        'label' => $suggestedLabel,
                        'start_date' => $suggestedStart,
                        'end_date' => $suggestedEnd,
                        'semester_1_start' => null,
                        'semester_1_end' => null,
                        'semester_2_start' => null,
                        'semester_2_end' => null,
                    ];
                @endphp
                @include('admin.academic-years._form')
                <div class="d-flex justify-content-end gap-1 mt-2">
                    <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-device-floppy me-1"></i> Save Academic Year
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
