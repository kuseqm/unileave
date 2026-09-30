@extends('layouts.master')

@section('title', 'HR Settings')

@section('content')
<div class="container-fluid px-2">

    <x-breadcrumb :items="[
        ['name' => 'Settings', 'url' => route('settings.index')],
        ['name' => 'HR Settings', 'url' => '#']
    ]" />

    <x-page-header title="HR SETTINGS" description="Configure human resources preferences">
        <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> All Settings
        </a>
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    @include('settings.hr._form', ['settings' => $settings])
</div>
@endsection
