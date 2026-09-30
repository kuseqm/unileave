@extends('layouts.master')

@section('title', 'Edit Academic Year')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Academic Years', 'url' => route('admin.academic-years.index')],
        ['name' => 'Edit', 'url' => route('admin.academic-years.edit', $academicYear->id)]
    ]" />

    <x-page-header title="EDIT ACADEMIC YEAR" description="{{ $academicYear->is_current ? 'This is the currently active academic year - changes apply immediately.' : 'This academic year is not active yet.' }}" />

    <x-alert-danger />

    <div class="card border-0 rounded-sm shadow-sm">
        <div class="card-body p-2">
            <form method="POST" action="{{ route('admin.academic-years.update', $academicYear->id) }}">
                @csrf
                @method('PUT')
                @include('admin.academic-years._form')
                <div class="d-flex justify-content-end gap-1 mt-2">
                    <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-device-floppy me-1"></i> Update Academic Year
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
