@extends('layouts.master')

@section('title', 'File Details - ' . $file->file_number)

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Registry', 'url' => '#'],
        ['name' => 'Letter Files', 'url' => route('registry.files.index')],
        ['name' => $file->file_number, 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="LETTER FILE DETAILS" :description="'File: ' . $file->file_number">
        <div class="d-flex gap-1">
            @if($file->is_active && !$file->closed_at && !$file->isFull())
            <form action="{{ route('registry.files.close', $file->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <button type="button" class="btn btn-warning btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;" onclick="confirmSubmit(this, 'Close this file? It will no longer accept new letters.', { danger: false, confirmText: 'Close File' })">
                    <i class="ti ti-lock me-1"></i> Close File
                </button>
            </form>
            @endif
            
            @if($file->closed_at && !$file->isFull())
            <form action="{{ route('registry.files.reopen', $file->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <button type="button" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;" onclick="confirmSubmit(this, 'Reopen this file? It will accept new letters again.', { danger: false, confirmText: 'Reopen File' })">
                    <i class="ti ti-lock-open me-1"></i> Reopen File
                </button>
            </form>
            @endif
            
            <a href="{{ route('registry.files.edit', $file->id) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-edit me-1"></i> Edit
            </a>
            <a href="{{ route('registry.files.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                <i class="ti ti-arrow-left me-1"></i> Back
            </a>
        </div>
    </x-page-header>
    
    <!-- Stats Cards -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $file->current_serial }}" label="Letters Filed" subtext="total in this file" color="primary" icon="file-text" />
        <x-stats-card value="{{ $file->remaining_capacity == 'Unlimited' ? '∞' : number_format($file->remaining_capacity) }}" label="Remaining" subtext="capacity left" color="success" icon="space" />
        <x-stats-card value="{{ $file->usage_percentage }}%" label="Usage" subtext="file capacity" color="warning" icon="chart-bar" />
        <x-stats-card value="{{ $file->letters()->count() }}" label="Total Letters" subtext="all letters" color="info" icon="files" />
    </div>
    
    <div class="row g-2">
        <!-- File Information -->
        <div class="col-lg-6">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">File Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.75rem;">
                        <tr>
                            <td class="text-muted ps-0" style="width: 35%;">File Number:</td>
                            <td class="fw-semibold font-monospace">{{ $file->file_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">File Name:</td>
                            <td>{{ $file->file_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Reference Format:</td>
                            <td>{{ $file->referenceFormat->name ?? 'N/A' }}</td>
                        </tr>
                        @if($file->referenceFormat)
                        <tr>
                            <td class="text-muted ps-0">Format Pattern:</td>
                            <td><code class="small">{{ $file->referenceFormat->format_pattern }}</code></td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted ps-0">Serial Range:</td>
                            <td>{{ $file->start_serial_formatted }} - {{ $file->end_serial_formatted }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Current Serial:</td>
                            <td class="fw-bold text-primary">{{ $file->current_serial_formatted }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Physical Location:</td>
                            <td>{{ $file->physical_location ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Status:</td>
                            <td>
                                @if($file->is_active && !$file->closed_at)
                                    <span class="badge bg-success-subtle text-success rounded-pill">Active</span>
                                @elseif($file->isFull())
                                    <span class="badge bg-warning-subtle text-warning rounded-pill">Full</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">Closed</span>
                                @endif
                             </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Created By:</td>
                            <td>{{ $file->creator->full_name ?? 'System' }} ({{ $file->created_at->format('d M Y H:i') }})</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Last Updated:</td>
                            <td>{{ $file->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                    
                    @if($file->notes)
                    <div class="mt-2">
                        <div class="text-muted" style="font-size: 0.6rem;">Notes:</div>
                        <div class="p-1 bg-light rounded-sm" style="font-size: 0.7rem;">{{ $file->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Usage Chart -->
        <div class="col-lg-6">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-pie text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Capacity Usage</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2 text-center">
                    <div class="position-relative d-inline-block mb-2">
                        <canvas id="usageChart" width="200" height="200" style="max-width: 200px;"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <div class="fw-bold" style="font-size: 1.5rem;">{{ $file->usage_percentage }}%</div>
                            <div class="text-muted" style="font-size: 0.6rem;">Used</div>
                        </div>
                    </div>
                    
                    <div class="row text-center mt-2">
                        <div class="col-4">
                            <div class="fw-bold text-primary">{{ $file->start_serial_formatted }}</div>
                            <div class="text-muted" style="font-size: 0.55rem;">Start</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-success">{{ $file->current_serial_formatted }}</div>
                            <div class="text-muted" style="font-size: 0.55rem;">Current</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-warning">{{ $file->end_serial_formatted }}</div>
                            <div class="text-muted" style="font-size: 0.55rem;">End</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Letters in this File -->
        <div class="col-12">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-files text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-weight: 600; font-size: 0.7rem;">Recent Letters in this File ({{ $recentLetters->count() }})</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentLetters->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1">Letter No.</th>
                                    <th class="py-1">Title</th>
                                    <th class="py-1">Date</th>
                                    <th class="py-1">From</th>
                                    <th class="pe-2 py-1 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLetters as $letter)
                                <tr>
                                    <td class="ps-2 py-1">
                                        <span class="fw-semibold font-monospace">{{ $letter->letter_number ?? 'N/A' }}</span>
                                    </td>
                                    <td class="py-1">{{ Str::limit($letter->title, 40) }}</td>
                                    <td class="py-1">{{ $letter->created_at->format('d M Y') }}</td>
                                    <td class="py-1">{{ $letter->sender->full_name ?? 'N/A' }}</td>
                                    <td class="pe-2 py-1 text-center">
                                        <a href="{{ route('letters.show', $letter->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="padding: 0.15rem 0.4rem; font-size: 0.6rem;">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                        <i class="ti ti-file mb-1 d-block" style="font-size: 1.2rem;"></i>
                        No letters have been filed in this volume yet.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Usage Chart
    const ctx = document.getElementById('usageChart')?.getContext('2d');
    if (ctx) {
        const used = {{ $file->usage_percentage }};
        const remaining = 100 - used;
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Used', 'Available'],
                datasets: [{
                    data: [used, remaining],
                    backgroundColor: ['#3B82F6', '#E2E8F0'],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 10 },
                            boxWidth: 10
                        }
                    },
                    tooltip: {
                        bodyFont: { size: 11 }
                    }
                }
            }
        });
    }
</script>
@endsection