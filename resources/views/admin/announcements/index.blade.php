@extends('layouts.master')

@section('title', 'Announcements')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Announcements', 'url' => route('admin.announcements.index')]
    ]" />

    <x-page-header title="ANNOUNCEMENTS" description="Notices shown on the public landing page - leave notices, campus events, academic calendar.">
        <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> New Announcement
        </a>
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="card border-0 rounded-sm shadow-sm mb-2">
        <div class="card-body p-2">
            <form method="GET" action="{{ route('admin.announcements.index') }}" class="d-flex flex-wrap align-items-end gap-2">
                <div>
                    <label class="form-label mb-0" style="font-size: 0.65rem;">Category</label>
                    <select name="category" class="form-select form-select-sm rounded-sm" style="font-size: 0.75rem;" onchange="this.form.submit()">
                        <option value="">All categories</option>
                        @foreach($categories as $c)
                            <option value="{{ $c }}" {{ $category === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label mb-0" style="font-size: 0.65rem;">Status</label>
                    <select name="status" class="form-select form-select-sm rounded-sm" style="font-size: 0.75rem;" onchange="this.form.submit()">
                        <option value="">All statuses</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                @if($category || $status)
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Clear filters</a>
                @endif
            </form>
        </div>
    </div>

    <div class="row g-2">
        <!-- Main Column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-speakerphone text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Announcements</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" style="font-size: 0.75rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-2" style="width: 60px;">Order</th>
                                    <th>Title</th>
                                    <th style="width: 150px;">Category &amp; Status</th>
                                    <th class="pe-2" style="width: 150px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($announcements as $a)
                                @php
                                    $statusStyles = [
                                        'live' => 'success',
                                        'scheduled' => 'info',
                                        'expired' => 'secondary',
                                        'inactive' => 'secondary',
                                    ];
                                    $statusLabels = [
                                        'live' => 'Live now',
                                        'scheduled' => 'Scheduled',
                                        'expired' => 'Expired',
                                        'inactive' => 'Inactive',
                                    ];
                                    $effectiveStatus = $a->effective_status;
                                @endphp
                                <tr>
                                    <td class="ps-2">
                                        <div class="d-flex flex-column gap-1">
                                            <form method="POST" action="{{ route('admin.announcements.move-up', $a->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;" title="Move up">
                                                    <i class="ti ti-chevron-up"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.announcements.move-down', $a->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;" title="Move down">
                                                    <i class="ti ti-chevron-down"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $a->title }}</strong>
                                        @if($a->is_pinned)
                                            <i class="ti ti-pin text-warning ms-1" style="font-size: 0.65rem;" title="Pinned"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ ucfirst($a->category) }}</span>
                                        <span class="badge bg-{{ $statusStyles[$effectiveStatus] }}-subtle text-{{ $statusStyles[$effectiveStatus] }} rounded-pill">
                                            {{ $statusLabels[$effectiveStatus] }}
                                        </span>
                                    </td>
                                    <td class="pe-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#announcementDetailsModal"
                                            data-title="{{ $a->title }}"
                                            data-body="{{ $a->body }}"
                                            data-category="{{ ucfirst($a->category) }}"
                                            data-pinned="{{ $a->is_pinned ? 'Yes' : 'No' }}"
                                            data-status="{{ $statusLabels[$effectiveStatus] }}"
                                            data-window="{{ ($a->starts_at?->format('d M Y') ?? 'Anytime') . ' to ' . ($a->expires_at?->format('d M Y') ?? 'No end') }}"
                                            title="View details">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                        <a href="{{ route('admin.announcements.edit', $a->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.announcements.destroy', $a->id) }}" class="d-inline" onsubmit="return confirm('Delete this announcement?')">
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
                                    <td colspan="4" class="text-center text-muted py-3">No announcements match these filters.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-1 px-2">
                    <x-pagination :items="$announcements" />
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
                            <i class="ti ti-arrows-vertical text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Display Order</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Use the up/down arrows to control the order announcements appear in on the public landing page.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-pin text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Pinned Always Comes First</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Pinned announcements are shown before all others on the landing page, regardless of their manual order.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-clock-bolt text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Status Is Calculated, Not Set</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Live / Scheduled / Expired is worked out automatically from the start and end dates you set - there's nothing to toggle manually.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-filter text-info mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Filtering This List</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Category and Status filters above only affect this admin list - they never hide anything from the public landing page.</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Announcement Details Modal -->
<div class="modal fade" id="announcementDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" id="announcementModalTitle" style="font-size: 0.8rem;"></h5>
                <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2" style="font-size: 0.75rem;">
                <p id="announcementModalBody" class="mb-2" style="white-space: pre-line;"></p>
                <dl class="row mb-0" style="font-size: 0.7rem;">
                    <dt class="col-5 text-muted fw-normal">Category</dt>
                    <dd class="col-7" id="announcementModalCategory"></dd>
                    <dt class="col-5 text-muted fw-normal">Pinned</dt>
                    <dd class="col-7" id="announcementModalPinned"></dd>
                    <dt class="col-5 text-muted fw-normal">Status</dt>
                    <dd class="col-7" id="announcementModalStatus"></dd>
                    <dt class="col-5 text-muted fw-normal">Window</dt>
                    <dd class="col-7" id="announcementModalWindow"></dd>
                </dl>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('announcementDetailsModal')?.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('announcementModalTitle').textContent = button.getAttribute('data-title');
    document.getElementById('announcementModalBody').textContent = button.getAttribute('data-body');
    document.getElementById('announcementModalCategory').textContent = button.getAttribute('data-category');
    document.getElementById('announcementModalPinned').textContent = button.getAttribute('data-pinned');
    document.getElementById('announcementModalStatus').textContent = button.getAttribute('data-status');
    document.getElementById('announcementModalWindow').textContent = button.getAttribute('data-window');
});
</script>
@endsection
