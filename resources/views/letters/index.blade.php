@extends('layouts.master')

@section('title', 'My Letters')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Letters', 'url' => '#']
    ]" />
    
    <x-page-header title="MY LETTERS" description="Manage your correspondence">
        <a href="{{ route('letters.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Write New Letter
        </a>
    </x-page-header>
    
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $stats['total'] }}" label="Total Letters" subtext="all time" />
        <x-stats-card value="{{ $stats['draft'] }}" label="Drafts" subtext="not submitted" color="warning" />
        <x-stats-card value="{{ $stats['pending_approval'] }}" label="Pending Approval" subtext="awaiting review" color="info" />
        <x-stats-card value="{{ $stats['approved'] }}" label="Approved" subtext="ready for signature" color="success" />
    </div>
    
    <div class="row g-2">
        <!-- Letters Table - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-files text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">All Letters</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1" width="50">#</th>
                                    <th class="py-1">Letter Number</th>
                                    <th class="py-1">Title</th>
                                    <th class="py-1">Type</th>
                                    <th class="py-1">Status</th>
                                    <th class="py-1">Date</th>
                                    <th class="pe-2 py-1" width="80">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($letters as $index => $letter)
                                <tr>
                                    <td class="ps-2 py-1">{{ $letters->firstItem() + $index }}</td>
                                    <td class="py-1">{{ $letter->letter_number ?? 'Draft' }}</td>
                                    <td class="py-1">{{ Str::limit($letter->title, 40) }}</td>
                                    <td class="py-1">
                                        <span class="badge bg-light text-dark rounded-sm">{{ $letter->type_label }}</span>
                                    </td>
                                    <td class="py-1">
                                        @include('letters.partials.status-badge', ['status' => $letter->stage])
                                    </td>
                                    <td class="py-1">{{ $letter->created_at->format('d M Y') }}</td>
                                    <td class="pe-2 py-1">
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('letters.show', $letter->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            @if($letter->stage === 'draft')
                                            <a href="{{ route('letters.edit', $letter->id) }}" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            @endif
                                            @if($letter->stage === 'signed')
                                            <a href="{{ route('letters.print', $letter->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">
                                                <i class="ti ti-printer"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted">No letters found. Click "Write New Letter" to create one.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($letters->hasPages())
                <div class="card-footer py-2 px-2 bg-light">
                    {{ $letters->links() }}
                </div>
                @endif
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> Understanding Status</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><span class="badge bg-secondary-subtle text-secondary rounded-sm">Draft</span> - not yet submitted</li>
                            <li><span class="badge bg-info-subtle text-info rounded-sm">Pending</span> - awaiting approval</li>
                            <li><span class="badge bg-success-subtle text-success rounded-sm">Approved</span> - ready for signature</li>
                            <li><span class="badge bg-primary-subtle text-primary rounded-sm">Signed</span> - final, ready to print</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('letters.create') }}">Write New Letter</a> - start a new letter</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Only draft letters can be edited</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Signed letters can be printed</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection