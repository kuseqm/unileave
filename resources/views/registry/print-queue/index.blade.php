@extends('layouts.master')

@section('title', 'Print Queue')

@section('content')
<div class="container-fluid px-2">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Central Registry', 'url' => '#'],
        ['name' => 'Print Queue', 'url' => '#']
    ]" />

    <!-- Page Header -->
    <x-page-header title="PRINT QUEUE" description="Pending, printed, and dispatched outgoing letters" />

    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $queue->total() }}" label="Pending" subtext="awaiting print" color="warning" icon="clock" />
        <x-stats-card value="{{ $overdue }}" label="Overdue" subtext="pending over 48h" color="danger" icon="alert-circle" />
        <x-stats-card value="{{ $printed->count() }}" label="Printed" subtext="last 20" color="success" icon="printer" />
        <x-stats-card value="{{ $dispatched->count() }}" label="Dispatched" subtext="last 20" color="info" icon="send" />
    </div>

    <!-- Search Card -->
    <x-search-card placeholder="Search by ref number or recipient..." id="printQueueSearchInput" name="search" :value="request('search')">
        <div id="printQueueSearchStats" class="text-muted" style="font-size: 0.65rem;">
            {{ $queue->total() }} pending jobs total
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Print jobs - main column -->
        <div class="col-lg-8">
            <!-- Pending Print Jobs -->
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-printer text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Pending Print Jobs</span>
                    </div>
                </div>
                <div id="printQueueResults">
                    @include('registry.print-queue._results')
                </div>
            </div>

            <!-- Recently Printed -->
            <div class="card rounded-sm mb-3">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-checkbox text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Recently Printed</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">Letter Ref</th>
                                    <th class="py-1">Recipient</th>
                                    <th class="pe-2 py-1">Printed At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($printed as $item)
                                <tr>
                                    <td class="ps-2 py-1"><strong>{{ $item->outgoingLetter->ref_number ?? 'N/A' }}</strong></td>
                                    <td class="py-1">{{ $item->recipient_address }}</td>
                                    <td class="pe-2 py-1">{{ $item->printed_at?->format('d/m/Y H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No recently printed letters.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recently Dispatched -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-send text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Recently Dispatched</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">Letter Ref</th>
                                    <th class="py-1">Recipient</th>
                                    <th class="pe-2 py-1">Dispatched At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dispatched as $item)
                                <tr>
                                    <td class="ps-2 py-1"><strong>{{ $item->outgoingLetter->ref_number ?? 'N/A' }}</strong></td>
                                    <td class="py-1">{{ $item->recipient_address }}</td>
                                    <td class="pe-2 py-1">{{ $item->dispatched_at?->format('d/m/Y H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No recently dispatched letters.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information - narrow column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> How the Queue Works</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>Only approved outgoing letters marked "Requires physical printing" land here</li>
                            <li>A job overdue past 48h is flagged so it doesn't get missed</li>
                            <li>Printed jobs move to Dispatched once handed off</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Use "Select All" to batch-print pending jobs</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; The Overdue count highlights jobs needing urgent attention</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        initLiveSearch({
            url: '{{ route('registry.print-queue.index') }}',
            resultsSelector: '#printQueueResults',
            statsSelector: '#printQueueSearchStats',
            searchInputSelector: '#printQueueSearchInput',
            suggestionsSelector: '#printQueueSearchInputSuggestions',
            recentSearchesKey: 'printQueueRecentSearches',
            statLabel: 'pending jobs',
            perPage: 20,
        });
    });

    // Delegated so it survives AJAX innerHTML swaps of #printQueueResults
    const printQueueResults = document.getElementById('printQueueResults');
    printQueueResults?.addEventListener('change', function (e) {
        if (e.target.id === 'selectAll') {
            printQueueResults.querySelectorAll('.job-checkbox').forEach(cb => cb.checked = e.target.checked);
        }
    });

    function printSingle(id) {
        fetch(`{{ url('registry/print-queue') }}/${id}/print`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => location.reload());
    }
</script>
@endsection
