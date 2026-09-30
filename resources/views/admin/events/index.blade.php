@extends('layouts.master')

@section('title', 'Events')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Events', 'url' => route('admin.events.index')]
    ]" />

    <x-page-header title="EVENTS" description="Calendar events shown on the public landing page.">
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> New Event
        </a>
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2">
        <!-- Main Column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar-event text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Events</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" style="font-size: 0.75rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-2" style="width: 110px;">Date</th>
                                    <th>Title</th>
                                    <th style="width: 90px;">Status</th>
                                    <th class="pe-2" style="width: 150px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $e)
                                <tr>
                                    <td class="ps-2 text-muted">{{ $e->event_date->format('d M Y') }}</td>
                                    <td><strong>{{ $e->title }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $e->is_active ? 'success' : 'secondary' }}-subtle text-{{ $e->is_active ? 'success' : 'secondary' }} rounded-pill">
                                            {{ $e->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="pe-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#eventDetailsModal"
                                            data-title="{{ $e->title }}"
                                            data-description="{{ $e->description }}"
                                            data-date="{{ $e->event_date->format('d M Y') }}"
                                            data-location="{{ $e->location ?? 'Not specified' }}"
                                            data-status="{{ $e->is_active ? 'Active' : 'Inactive' }}"
                                            title="View details">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                        <a href="{{ route('events.show', $e->slug) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;" title="View public page">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                        <a href="{{ route('admin.events.edit', $e->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.events.destroy', $e->id) }}" class="d-inline" onsubmit="return confirm('Delete this event?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No events yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-1 px-2">
                    <x-pagination :items="$events" />
                </div>
            </div>
        </div>

        <!-- Info Column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-calendar-stats text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Feeds the Landing Page Calendar</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Every event created here appears on the public calendar shown on the landing page, on its event date.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-external-link text-info mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Preview Before You Share</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Use the external-link icon to open an event's public page exactly as visitors will see it.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-eye-off text-secondary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Inactive Events Stay Hidden</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Marking an event Inactive removes it from the public calendar without deleting it, useful for postponed or draft events.</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" id="eventModalTitle" style="font-size: 0.8rem;"></h5>
                <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2" style="font-size: 0.75rem;">
                <p id="eventModalDescription" class="mb-2" style="white-space: pre-line;"></p>
                <dl class="row mb-0" style="font-size: 0.7rem;">
                    <dt class="col-5 text-muted fw-normal">Date</dt>
                    <dd class="col-7" id="eventModalDate"></dd>
                    <dt class="col-5 text-muted fw-normal">Location</dt>
                    <dd class="col-7" id="eventModalLocation"></dd>
                    <dt class="col-5 text-muted fw-normal">Status</dt>
                    <dd class="col-7" id="eventModalStatus"></dd>
                </dl>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('eventDetailsModal')?.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('eventModalTitle').textContent = button.getAttribute('data-title');
    document.getElementById('eventModalDescription').textContent = button.getAttribute('data-description');
    document.getElementById('eventModalDate').textContent = button.getAttribute('data-date');
    document.getElementById('eventModalLocation').textContent = button.getAttribute('data-location');
    document.getElementById('eventModalStatus').textContent = button.getAttribute('data-status');
});
</script>
@endsection
