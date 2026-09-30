@extends('layouts.master')

@section('title', 'Edit Policy')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'University Policies', 'url' => route('policies.index')],
        ['name' => $policy->title, 'url' => route('policies.show', $policy)],
        ['name' => 'Edit', 'url' => '#']
    ]" />

    <x-page-header title="EDIT POLICY" description="Update policy details" backUrl="{{ route('policies.show', $policy) }}">
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <form method="POST" action="{{ route('policies.update', $policy) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card rounded-sm">
            <div class="card-header py-1 px-2 bg-light">
                <span style="font-size: 0.7rem; font-weight: 600;">Policy Details</span>
            </div>
            <div class="card-body py-2 px-3">
                @include('policies._form', ['policy' => $policy])
                <button type="submit" class="btn btn-primary btn-sm rounded-sm">Update Policy</button>
                <a href="{{ route('policies.show', $policy) }}" class="btn btn-secondary btn-sm rounded-sm">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
