@extends('layouts.master')

@section('title', 'Rule Change History')

@php
    // A remembered rule in words: "45 days per year - Staff category is Academic".
    $describe = function (?array $snapshot) use ($catalog) {
        if (! $snapshot) {
            return null;
        }

        $rule = new \App\Models\PolicyRule($snapshot);

        return $catalog->describeValue($rule).' - '.$catalog->describeCondition($rule).($rule->is_active ? '' : ' - inactive');
    };
    $actionBadge = ['created' => 'bg-success', 'updated' => 'bg-primary', 'deleted' => 'bg-danger'];
@endphp

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'University Policies', 'url' => route('policies.index')],
        ['name' => $policy->title, 'url' => route('policies.show', $policy)],
        ['name' => 'Change history', 'url' => '#']
    ]" />

    <x-page-header title="RULE CHANGE HISTORY" description="Every change to the rules of {{ $policy->code }}: what it was, what it became, who changed it and why" backUrl="{{ route('policies.show', $policy) }}">
    </x-page-header>

    <div class="card rounded-sm">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0" style="font-size: 0.75rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 11rem;">When</th>
                        <th style="width: 10rem;">Who</th>
                        <th>Change</th>
                        <th style="width: 25%;">Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($changes as $change)
                    <tr>
                        <td>{{ $change->created_at->format('d M Y, H:i') }}</td>
                        <td>{{ $change->changer?->full_name ?? 'Unknown' }}</td>
                        <td>
                            <span class="badge {{ $actionBadge[$change->action] ?? 'bg-secondary' }}">{{ ucfirst($change->action) }}</span>
                            <strong>{{ $catalog->label($change->key) }}</strong>
                            @if($change->old_values)
                                <div class="text-muted" style="font-size: 0.65rem;">Was: {{ $describe($change->old_values) }}</div>
                            @endif
                            @if($change->new_values)
                                <div style="font-size: 0.65rem;">{{ $change->action === 'created' ? 'Set to' : 'Now' }}: {{ $describe($change->new_values) }}</div>
                            @endif
                        </td>
                        <td>{{ $change->reason ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">No rule of this policy has been changed since changes started being recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2">{{ $changes->links() }}</div>
    </div>
</div>
@endsection
