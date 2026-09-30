@extends('layouts.master')

@section('title', 'New Announcement')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Announcements', 'url' => route('admin.announcements.index')],
        ['name' => 'New Announcement', 'url' => route('admin.announcements.create')]
    ]" />

    <x-page-header title="NEW ANNOUNCEMENT" description="This will appear on the public landing page once saved (if active and within its date window)." />

    <x-alert-danger />

    <div class="row g-2">
        <div class="col-lg-7">
            <div class="card border-0 rounded-sm shadow-sm">
                <div class="card-body p-2">
                    <form method="POST" action="{{ route('admin.announcements.store') }}">
                        @csrf
                        @include('admin.announcements._form', ['announcement' => null])
                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-device-floppy me-1"></i> Save Announcement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            @include('admin.announcements._preview')
        </div>
    </div>
</div>
@endsection
