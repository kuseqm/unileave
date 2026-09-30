@extends('layouts.master')

@section('title', 'Edit Event')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Events', 'url' => route('admin.events.index')],
        ['name' => 'Edit', 'url' => route('admin.events.edit', $event->id)]
    ]" />

    <x-page-header title="EDIT EVENT" description="Changes apply immediately on the public landing page calendar." />

    <x-alert-danger />

    <div class="card border-0 rounded-sm shadow-sm">
        <div class="card-body p-2">
            <form method="POST" action="{{ route('admin.events.update', $event->id) }}">
                @csrf
                @method('PUT')
                @include('admin.events._form', ['event' => $event])
                <div class="d-flex justify-content-end gap-1 mt-2">
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-device-floppy me-1"></i> Update Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
