@extends('layouts.master')

@section('title', 'Add Policy')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'University Policies', 'url' => route('policies.index')],
        ['name' => 'Add Policy', 'url' => '#']
    ]" />

    <x-page-header title="ADD POLICY" description="Create a new institutional policy, handbook, or regulation" backUrl="{{ route('policies.index') }}">
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <form method="POST" action="{{ route('policies.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="card rounded-sm">
            <div class="card-header py-1 px-2 bg-light">
                <span style="font-size: 0.7rem; font-weight: 600;">Policy Details</span>
            </div>
            <div class="card-body py-2 px-3">
                @role('super-admin')
                <div class="form-check mb-3" style="font-size: 0.75rem;">
                    <input class="form-check-input" type="checkbox" name="is_platform" value="1" id="is_platform" {{ old('is_platform') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_platform">
                        Create as a <strong>platform default</strong> - inherited by every university that hasn't published its own version.
                        Leave unticked to create it for your own university only.
                    </label>
                </div>
                @endrole
                @include('policies._form')
                <button type="submit" class="btn btn-primary btn-sm rounded-sm">Save Draft</button>
                <a href="{{ route('policies.index') }}" class="btn btn-secondary btn-sm rounded-sm">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
