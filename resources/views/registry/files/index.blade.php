@extends('layouts.master')

@section('title', 'Letter Files')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Registry', 'url' => '#'],
        ['name' => 'Letter Files', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="LETTER FILES" description="Manage physical letter files and volumes">
        <a href="{{ route('registry.files.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> Add File
        </a>
    </x-page-header>
    
    <!-- Stats Cards -->
    @php
        $totalFiles = \App\Models\LetterFile::count();
        $activeFiles = \App\Models\LetterFile::active()->count();
        $fullFiles = \App\Models\LetterFile::where('is_active', true)->get()->filter(function($f) { return $f->isFull(); })->count();
        $totalLetters = \App\Models\Letter::count();
    @endphp
    
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $totalFiles }}" label="Total Files" subtext="all volumes" color="primary" icon="folder" />
        <x-stats-card value="{{ $activeFiles }}" label="Active Files" subtext="accepting letters" color="success" icon="folder-open" />
        <x-stats-card value="{{ $fullFiles }}" label="Full Files" subtext="at capacity" color="warning" icon="folder-filled" />
        <x-stats-card value="{{ number_format($totalLetters) }}" label="Total Letters" subtext="across all files" color="info" icon="files" />
    </div>
    
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <i class="ti ti-files text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Letter Files List</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    Showing {{ $files->firstItem() ?? 0 }} to {{ $files->lastItem() ?? 0 }} of {{ $files->total() ?? 0 }} files
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">#</th>
                            <th class="py-1">File Number</th>
                            <th class="py-1">Name</th>
                            <th class="py-1">Format</th>
                            <th class="py-1">Serial Range</th>
                            <th class="py-1" width="100">Usage</th>
                            <th class="py-1" width="80">Status</th>
                            <th class="pe-2 py-1 text-center" width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($files as $index => $file)
                        <tr class="clickable-row" style="cursor: pointer;" onclick="window.location='{{ route('registry.files.show', $file->id) }}'">
                            <td class="ps-2 py-1">{{ $files->firstItem() + $index }}</td>
                            <td class="py-1">
                                <span class="fw-semibold font-monospace hover-link">{{ $file->file_number }}</span>
                            </td>
                            <td class="py-1">{{ Str::limit($file->file_name, 30) }}</td>
                            <td class="py-1">{{ $file->referenceFormat->name ?? 'N/A' }}</td>
                            <td class="py-1 font-monospace">{{ $file->start_serial_formatted }} - {{ $file->end_serial_formatted }}</td>
                            <td class="py-1">
                                <div class="d-flex align-items-center gap-1">
                                    <div class="progress flex-grow-1" style="height: 4px;">
                                        <div class="progress-bar {{ $file->usage_percentage >= 90 ? 'bg-danger' : ($file->usage_percentage >= 70 ? 'bg-warning' : 'bg-success') }}" 
                                             style="width: {{ $file->usage_percentage }}%"></div>
                                    </div>
                                    <span class="text-muted" style="font-size: 0.55rem;">{{ $file->usage_percentage }}%</span>
                                </div>
                            </td>
                            <td class="py-1">
                                @if($file->is_active && !$file->closed_at)
                                    <span class="badge bg-success-subtle text-success rounded-pill">Active</span>
                                @elseif($file->isFull())
                                    <span class="badge bg-warning-subtle text-warning rounded-pill">Full</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">Closed</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1 text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <!-- View Button -->
                                    <a href="{{ route('registry.files.show', $file->id) }}" 
                                       class="btn btn-sm btn-outline-info rounded-sm" 
                                       onclick="event.stopPropagation();"
                                       style="padding: 0.15rem 0.4rem; font-size: 0.6rem;" 
                                       title="View File">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    
                                    <!-- Edit Button -->
                                    <a href="{{ route('registry.files.edit', $file->id) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-sm" 
                                       onclick="event.stopPropagation();"
                                       style="padding: 0.15rem 0.4rem; font-size: 0.6rem;" 
                                       title="Edit File">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    
                                    <!-- Close Button (only for active non-full files) -->
                                    @if($file->is_active && !$file->closed_at && !$file->isFull())
                                    <button type="button" 
                                            onclick="event.stopPropagation(); showCloseFileModal('{{ $file->id }}', '{{ addslashes($file->file_number) }}', '{{ route('registry.files.close', $file->id) }}')" 
                                            class="btn btn-sm btn-outline-warning rounded-sm" 
                                            style="padding: 0.15rem 0.4rem; font-size: 0.6rem;" 
                                            title="Close File">
                                        <i class="ti ti-lock"></i>
                                    </button>
                                    @endif
                                    
                                    <!-- Reopen Button (only for closed files that aren't full) -->
                                    @if($file->closed_at && !$file->isFull())
                                    <button type="button" 
                                            onclick="event.stopPropagation(); showReopenFileModal('{{ $file->id }}', '{{ addslashes($file->file_number) }}', '{{ route('registry.files.reopen', $file->id) }}')" 
                                            class="btn btn-sm btn-outline-success rounded-sm" 
                                            style="padding: 0.15rem 0.4rem; font-size: 0.6rem;" 
                                            title="Reopen File">
                                        <i class="ti ti-lock-open"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="ti ti-folder mb-2 d-block" style="font-size: 1.5rem;"></i>
                                <p>No letter files found</p>
                                <a href="{{ route('registry.files.create') }}" class="btn btn-sm btn-primary rounded-sm">Create First File</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($files->hasPages())
        <div class="card-footer py-1 px-2">
            {{ $files->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.clickable-row {
    transition: background 0.2s ease;
}
.clickable-row:hover {
    background-color: #F8FAFC !important;
}
.hover-link:hover {
    text-decoration: underline;
    color: #3B82F6 !important;
}
.progress {
    background-color: #E2E8F0;
    border-radius: 4px;
    overflow: hidden;
}
.progress-bar {
    transition: width 0.3s ease;
}
</style>

<!-- Close File Confirmation Modal -->
<div class="modal fade" id="closeFileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2 bg-warning-subtle">
                <h5 class="modal-title" style="font-size: 0.8rem;">
                    <i class="ti ti-alert-triangle text-warning me-1"></i> Close File
                </h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to close <strong id="closeFileName"></strong>?</p>
                <div class="alert alert-warning py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                    <i class="ti ti-alert-circle me-1"></i>
                    <strong>Warning:</strong> Once closed, this file will no longer accept new letters.
                </div>
                <div class="alert alert-info py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                    <i class="ti ti-info-circle me-1"></i>
                    You can reopen the file later if needed.
                </div>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <form action="" id="closeFileForm" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-warning btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-lock me-1"></i> Close File
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reopen File Confirmation Modal -->
<div class="modal fade" id="reopenFileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2 bg-success-subtle">
                <h5 class="modal-title" style="font-size: 0.8rem;">
                    <i class="ti ti-alert-triangle text-success me-1"></i> Reopen File
                </h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to reopen <strong id="reopenFileName"></strong>?</p>
                <div class="alert alert-success py-1 px-2 mt-2 rounded-sm" style="font-size: 0.65rem;">
                    <i class="ti ti-check-circle me-1"></i>
                    The file will be active and can accept new letters again.
                </div>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <form action="" id="reopenFileForm" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-lock-open me-1"></i> Reopen File
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showCloseFileModal(fileId, fileName, closeRoute) {
        document.getElementById('closeFileName').innerText = fileName;
        document.getElementById('closeFileForm').action = closeRoute;
        new bootstrap.Modal(document.getElementById('closeFileModal')).show();
    }
    
    function showReopenFileModal(fileId, fileName, reopenRoute) {
        document.getElementById('reopenFileName').innerText = fileName;
        document.getElementById('reopenFileForm').action = reopenRoute;
        new bootstrap.Modal(document.getElementById('reopenFileModal')).show();
    }
</script>
@endsection