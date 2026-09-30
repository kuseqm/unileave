@extends('layouts.master')

@section('title', 'Applications Overview')

@section('content')
<style>
    #overviewMainTabs .nav-link {
        color: #0d6efd;
    }
    #overviewMainTabs .nav-link.active {
        background-color: #e3f2fd !important;
        color: #0d6efd !important;
        border-bottom-color: #b6d4fe !important;
    }
</style>

@php
    $activeCounts = $activeTab === 'completed' ? $completedCounts : $pendingCounts;
    $panelId = $activeTab === 'completed' ? 'completed' : 'pending';
@endphp

<div class="container-fluid px-2">

    <x-breadcrumb :items="[
        ['name' => 'Applications Overview', 'url' => '#']
    ]" />

    <x-page-header title="APPLICATIONS OVERVIEW" description="Every leave, permission, and travel request - read-only, cannot approve or reject">
    </x-page-header>

    <x-alert-success />

    <x-alert-danger />

    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
            <ul class="nav nav-tabs card-header-tabs" id="overviewMainTabs" role="tablist" style="font-size: 0.85rem;">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'in-progress' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(array_merge(request()->except('page'), ['tab' => 'in-progress', 'subtab' => 'all'])) }}" role="tab">
                        <i class="ti ti-clock me-1"></i> In Progress
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'completed' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(array_merge(request()->except('page'), ['tab' => 'completed', 'subtab' => 'all'])) }}" role="tab">
                        <i class="ti ti-check me-1"></i> Completed
                    </a>
                </li>
            </ul>

            <span class="badge bg-primary rounded-pill" style="font-size: 0.8rem;">
                {{ $activeCounts['total'] ?? 0 }} total
            </span>
        </div>
        <div class="card-body p-0">
            @include('shared.applications-overview._panel', [
                'id' => $panelId,
                'activeTab' => $activeTab,
                'activeSubtab' => $activeSubtab,
                'stats' => $activeStats,
                'paginator' => $paginator,
                'counts' => $activeCounts,
            ])
        </div>
    </div>
</div>
@endsection
