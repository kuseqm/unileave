@extends('layouts.master')

@section('title', 'New Event')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Events', 'url' => route('admin.events.index')],
        ['name' => 'New Event', 'url' => route('admin.events.create')]
    ]" />

    <x-page-header title="NEW EVENT" description="This will appear on the public landing page calendar once saved (if active)." />

    <x-alert-danger />

    <div class="card border-0 rounded-sm shadow-sm">
        <div class="card-body p-2">
            <form method="POST" action="{{ route('admin.events.store') }}">
                @csrf
                @include('admin.events._form', ['event' => null])
                <div class="d-flex justify-content-end gap-1 mt-2">
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-device-floppy me-1"></i> Save Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
