@extends('layouts.master')

@section('title', 'Edit Team Member')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Team Members', 'url' => route('admin.team-members.index')],
        ['name' => 'Edit', 'url' => route('admin.team-members.edit', $member->id)]
    ]" />

    <x-page-header title="EDIT TEAM MEMBER" description="Changes apply immediately on the public landing page." />

    <x-alert-danger />

    <div class="card border-0 rounded-sm shadow-sm">
        <div class="card-body p-2">
            <form method="POST" action="{{ route('admin.team-members.update', $member->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.team-members._form', ['member' => $member])
                <div class="d-flex justify-content-end gap-1 mt-2">
                    <a href="{{ route('admin.team-members.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-device-floppy me-1"></i> Update Team Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
