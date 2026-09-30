@extends('layouts.master')

@section('title', 'Registry Settings')

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Registry', 'url' => '#'],
        ['name' => 'Settings', 'url' => route('settings.index')]
    ]" />

    <!-- Page Header -->
    <x-page-header title="REGISTRY SETTINGS" description="Configure letter registry system settings">
        <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> All Settings
        </a>
    </x-page-header>

    <x-alert-success />

    <x-alert-danger />

    @include('registry._settings-form', ['universities' => $universities, 'settings' => $settings, 'selectedUniversity' => $selectedUniversity])
</div>
@endsection
