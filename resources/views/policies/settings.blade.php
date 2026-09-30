@extends('layouts.master')

@section('title', 'Policy Settings')

@php
    $universityName = auth()->user()->university?->name ?? 'the platform';
    $sourceBadge = [
        'university' => ['bg-success', 'Your university'],
        'platform' => ['bg-secondary', 'Platform default'],
        'default' => ['bg-light text-dark border', 'System default'],
    ];
    $enforcementBadge = ['enforced' => 'bg-success', 'display' => 'bg-info text-dark', 'planned' => 'bg-warning text-dark', 'reference' => 'bg-secondary'];
    $actionBadge = ['created' => 'bg-success', 'updated' => 'bg-primary', 'deleted' => 'bg-danger'];
@endphp

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'University Policies', 'url' => route('policies.index')],
        ['name' => 'Settings', 'url' => '#']
    ]" />

    <x-page-header title="POLICY SETTINGS" :description="'What the system applies at '.$universityName.', and where each value comes from'" backUrl="{{ route('policies.index') }}">
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    {{-- Whose values these are, and whether the university's own policy is actually in force. --}}
    @if(! $ownPolicy)
        <div class="alert alert-info py-2 px-3" style="font-size: 0.7rem;">
            {{ $universityName }} has no {{ $categories[$category] }} of its own yet, so every value below is the platform default
            or the system's built-in one.
            @if($platformPolicy)
                To set your own values, copy <a href="{{ route('policies.show', $platformPolicy) }}">{{ $platformPolicy->code }}</a>
                to your university - your copy goes through your own approval before it takes effect.
            @endif
        </div>
    @elseif(! $ownPolicy->isActive())
        <div class="alert alert-warning py-2 px-3" style="font-size: 0.7rem;">
            Your policy <a href="{{ route('policies.show', $ownPolicy) }}">{{ $ownPolicy->code }}</a> is
            <strong>{{ strtolower($ownPolicy->status) }}</strong>, not in force. Values you set in it apply only once it is approved and published;
            until then the values below still come from the platform.
        </div>
    @endif

    <ul class="nav nav-pills mb-2" style="font-size: 0.7rem;">
        @foreach($categories as $key => $label)
            <li class="nav-item">
                <a class="nav-link py-1 px-2 {{ $category === $key ? 'active' : '' }}" href="{{ route('policies.settings', ['category' => $key]) }}">{{ $label }}</a>
            </li>
        @endforeach
    </ul>

    @if($category === 'hr_policy')
    {{-- "What would apply to ...": settings can depend on the kind of staff member or leave. --}}
    <div class="card rounded-sm mb-2">
        <div class="card-body py-2 px-3">
            <form method="GET" action="{{ route('policies.settings') }}" class="row g-2 align-items-end">
                <input type="hidden" name="category" value="{{ $category }}">
                @foreach($contextFields as $field => $meta)
                    <div class="col-md-3">
                        <label class="form-label mb-0" style="font-size: 0.65rem;">{{ $meta['label'] }}</label>
                        <select name="{{ $field }}" class="form-select form-select-sm rounded-sm">
                            <option value="">Anyone / not specified</option>
                            @foreach($meta['options'] as $value => $optionLabel)
                                <option value="{{ $value }}" {{ ($filters[$field] ?? null) === (string) $value ? 'selected' : '' }}>{{ $optionLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm">Show what applies</button>
                    @if($filters)
                        <a href="{{ route('policies.settings', ['category' => $category]) }}" class="btn btn-outline-secondary btn-sm rounded-sm">Reset</a>
                    @endif
                </div>
            </form>
            <div class="text-muted mt-1" style="font-size: 0.6rem;">
                Some settings differ by staff category, contract type or leave type. Pick who you mean to see the value that would apply to them;
                a setting that doesn't depend on a choice ignores it.
            </div>
        </div>
    </div>
    @endif

    @foreach($groups as $group => $rows)
    <div class="card rounded-sm mb-2">
        <div class="card-header py-1 px-2 bg-light">
            <span style="font-size: 0.7rem; font-weight: 600;">{{ $group }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle" style="font-size: 0.75rem;">
                <thead>
                    <tr class="text-muted" style="font-size: 0.65rem;">
                        <th style="width: 34%;">Setting</th>
                        <th style="width: 20%;">Value in force</th>
                        <th style="width: 20%;">Comes from</th>
                        <th style="width: 14%;">System use</th>
                        <th class="text-end" style="width: 12%;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $row['label'] }}</div>
                            @if($row['description'])
                                <div class="text-muted" style="font-size: 0.62rem;">{{ $row['description'] }}</div>
                            @endif
                            @if(count($row['rules']) > 0)
                                <details class="mt-1" style="font-size: 0.65rem;">
                                    <summary class="text-muted" style="cursor: pointer;">{{ count($row['rules']) }} {{ \Illuminate\Support\Str::plural('rule', count($row['rules'])) }} defined</summary>
                                    <ul class="mb-0 ps-3">
                                        @foreach($row['rules'] as $rule)
                                            <li class="{{ $rule['in_force'] ? 'fw-semibold' : 'text-muted' }}">
                                                {{ $rule['value'] }} - {{ $rule['applies_to'] }}
                                                <span class="text-muted">({{ $rule['source'] === 'university' ? 'your university' : 'platform' }}, {{ $rule['policy'] }})</span>
                                                @if($rule['in_force']) <span class="badge bg-success">in force here</span> @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </details>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $row['value'] }}</td>
                        <td>
                            <span class="badge {{ $sourceBadge[$row['source']][0] }}">{{ $sourceBadge[$row['source']][1] }}</span>
                            @if($row['policy'])
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $row['policy'] }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $enforcementBadge[$row['enforcement']] ?? 'bg-secondary' }}">{{ $row['enforcement_label'] }}</span>
                        </td>
                        <td class="text-end">
                            @if($row['action'])
                                <a href="{{ $row['action']['url'] }}" class="btn btn-outline-primary btn-xs rounded-sm py-0 px-1">{{ $row['action']['label'] }}</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    <div class="card rounded-sm mb-3">
        <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
            <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-history me-1"></i> Recent changes</span>
            @if($ownPolicy)
                <a href="{{ route('policies.changes', $ownPolicy) }}" class="btn btn-outline-secondary btn-xs rounded-sm py-0 px-1">All changes to {{ $ownPolicy->code }}</a>
            @endif
        </div>
        <div class="card-body py-2 px-3" style="font-size: 0.7rem;">
            @forelse($recentChanges as $change)
                <div class="d-flex gap-2 py-1 {{ $loop->last ? '' : 'border-bottom' }}">
                    <span class="text-muted" style="width: 8.5rem;">{{ $change->created_at->format('d M Y, H:i') }}</span>
                    <span>
                        <span class="badge {{ $actionBadge[$change->action] ?? 'bg-secondary' }}">{{ ucfirst($change->action) }}</span>
                        <strong>{{ $catalog->label($change->key) }}</strong>
                        in <a href="{{ route('policies.changes', $change->policy) }}">{{ $change->policy->code }}</a>
                        by {{ $change->changer?->full_name ?? 'Unknown' }}
                        @if($change->reason)<span class="text-muted"> - {{ $change->reason }}</span>@endif
                    </span>
                </div>
            @empty
                <span class="text-muted">No setting has been changed yet.</span>
            @endforelse
        </div>
    </div>
</div>
@endsection
