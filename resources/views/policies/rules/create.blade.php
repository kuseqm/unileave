@extends('layouts.master')

@section('title', 'Add Policy Rule')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'University Policies', 'url' => route('policies.index')],
        ['name' => $policy->title, 'url' => route('policies.show', $policy)],
        ['name' => 'Add Rule', 'url' => '#']
    ]" />

    <x-page-header title="ADD POLICY RULE" description="Define a dynamic rule for {{ $policy->code }}" backUrl="{{ route('policies.rules.index', $policy) }}">
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <form method="POST" action="{{ route('policies.rules.store', $policy) }}">
        @csrf
        <div class="card rounded-sm">
            <div class="card-header py-1 px-2 bg-light">
                <span style="font-size: 0.7rem; font-weight: 600;">Rule Definition</span>
            </div>
            <div class="card-body py-2 px-3">
                @include('policies.rules._form')
                <button type="submit" class="btn btn-primary btn-sm rounded-sm">Save Rule</button>
                <a href="{{ route('policies.rules.index', $policy) }}" class="btn btn-secondary btn-sm rounded-sm">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
