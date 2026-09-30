@extends('layouts.master')

@section('title', 'Letterhead Settings')

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Admin', 'url' => '#'],
        ['name' => 'Settings', 'url' => route('settings.index')],
        ['name' => 'Letterhead', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="LETTERHEAD SETTINGS" description="Configure university letterhead branding">
        <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> All Settings
        </a>
    </x-page-header>

    <x-alert-success />

    @include('admin.settings._letterhead-form', ['university' => $university])
</div>
@endsection
