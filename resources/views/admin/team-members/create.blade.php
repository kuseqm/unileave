@extends('layouts.master')

@section('title', 'New Team Member')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Team Members', 'url' => route('admin.team-members.index')],
        ['name' => 'New Team Member', 'url' => route('admin.team-members.create')]
    ]" />

    <x-page-header title="NEW TEAM MEMBER" description="This will appear in the 'Built by' section on the public landing page once saved." />

    <x-alert-danger />

    <div class="card border-0 rounded-sm shadow-sm">
        <div class="card-body p-2">
            <form method="POST" action="{{ route('admin.team-members.store') }}" enctype="multipart/form-data">
                @csrf
                @include('admin.team-members._form', ['member' => null])
                <div class="d-flex justify-content-end gap-1 mt-2">
                    <a href="{{ route('admin.team-members.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-device-floppy me-1"></i> Save Team Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
