    <!-- ID Cards Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-id text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">ID Cards List</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">SN</th>
                            <th class="py-1">Staff</th>
                            <th class="py-1" width="120">Staff ID</th>
                            <th class="py-1" width="100">Issued</th>
                            <th class="py-1" width="100">Valid Until</th>
                            <th class="py-1" width="90">Status</th>
                            <th class="pe-2 py-1" width="110">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($idCards as $index => $idCard)
                        <tr>
                            <td class="ps-2 py-1">{{ $idCards->firstItem() + $index }}</td>
                            <td class="py-1">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light me-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                        <i class="ti ti-user" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <span class="fw-semibold">{{ $idCard->staff?->full_name ?? 'Unknown staff' }}</span>
                                </div>
                            </td>
                            <td class="py-1">{{ $idCard->staff?->staff_id }}</td>
                            <td class="py-1">{{ $idCard->issued_at?->format('d M Y') }}</td>
                            <td class="py-1">{{ $idCard->valid_until?->format('d M Y') }}</td>
                            <td class="py-1">
                                @if($idCard->valid_until && $idCard->valid_until->isPast())
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Expired</span>
                                @else
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Valid</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.id-cards.show', $idCard->id) }}" class="btn btn-sm btn-outline-info rounded-sm" title="Preview" style="padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-eye" style="font-size: 0.7rem;"></i>
                                    </a>
                                    <a href="{{ route('id-cards.print', $idCard->id) }}?autoprint=1" target="_blank" class="btn btn-sm btn-outline-primary rounded-sm" title="Print" style="padding: 0.15rem 0.3rem;">
                                        <i class="ti ti-printer" style="font-size: 0.7rem;"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-sm" title="Delete" style="padding: 0.15rem 0.3rem;" onclick="showDeleteModal('{{ $idCard->id }}', '{{ addslashes($idCard->staff?->full_name) }}', null, '{{ route('admin.id-cards.destroy', $idCard->id) }}')">
                                        <i class="ti ti-trash" style="font-size: 0.7rem;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                No ID cards found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <x-pagination :items="$idCards" :perPageOptions="[10, 25, 50, 100]" :ajax="true" />
        </div>
    </div>
