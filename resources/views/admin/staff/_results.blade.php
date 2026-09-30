    <!-- Staff Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <i class="ti ti-users text-primary me-1" style="font-size: 0.8rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 600;">Staff Directory</span>
                </div>
                <div class="text-muted" style="font-size: 0.6rem;">
                    Showing {{ $staffMembers->firstItem() ?? 0 }} to {{ $staffMembers->lastItem() ?? 0 }} of {{ $staffMembers->total() ?? 0 }} entries
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;" id="staffTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">SN</th>
                            <th class="py-1">Name</th>
                            <th class="py-1">Email</th>
                            <th class="py-1">Department</th>
                            <th class="py-1">Category</th>
                            <th class="py-1">Status</th>
                            {{-- Show Actions column for admin, HR, or Dean --}}
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager') || auth()->user()->hasRole('dean'))
                            <th class="pe-2 py-1" width="100">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffMembers as $index => $staff)
                        <tr data-staff-id="{{ $staff->id }}"
                            data-name="{{ strtolower($staff->full_name) }}"
                            data-email="{{ strtolower($staff->email) }}"
                            data-staff-code="{{ strtolower($staff->staff_id ?? '') }}"
                            data-category="{{ $staff->staff_category }}"
                            data-status="{{ $staff->status }}">
                            <td class="ps-2 py-1">{{ $staffMembers->firstItem() + $index }}</td>
                            <td class="py-1">
                                <span class="fw-semibold">{{ $staff->full_name }}</span>
                            </td>
                            <td class="py-1">{{ $staff->email }}</td>
                            <td class="py-1">{{ $staff->department->name ?? 'N/A' }}</td>
                            <td class="py-1">
                                @if($staff->staff_category == 'academic')
                                    <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.6rem;">Academic</span>
                                @elseif($staff->staff_category == 'administrative')
                                    <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">Administrative</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Supporting</span>
                                @endif
                            </td>
                            <td class="py-1">
                                @if($staff->status == 'active')
                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Active</span>
                                @elseif($staff->status == 'inactive')
                                    <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Inactive</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Suspended</span>
                                @endif
                            </td>
                            {{-- Show action buttons for admin, HR, or Dean --}}
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager') || auth()->user()->hasRole('dean'))
                            <td class="pe-2 py-1">
                                <div class="d-flex gap-1">
                                    <!-- View Button (always visible for everyone) -->
                                    <a href="{{ route('staff.show', $staff->id) }}"
                                       class="btn btn-sm btn-outline-info rounded-sm"
                                       style="font-size: 0.6rem; padding: 0.15rem 0.4rem;"
                                       title="View Staff">
                                        <i class="ti ti-eye"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    <a href="{{ route('staff.edit', $staff->id) }}"
                                       class="btn btn-sm btn-outline-primary rounded-sm"
                                       style="font-size: 0.6rem; padding: 0.15rem 0.4rem;"
                                       title="Edit Staff">
                                        <i class="ti ti-edit"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <button type="button"
                                            onclick="showDeleteModal({{ $staff->id }}, '{{ addslashes($staff->full_name) }}', 'staff', '{{ route('staff.destroy', $staff->id) }}')"
                                            class="btn btn-sm btn-outline-danger rounded-sm"
                                            style="font-size: 0.6rem; padding: 0.15rem 0.4rem;"
                                            title="Delete Staff">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager') || auth()->user()->hasRole('dean'))
                            <td colspan="7" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No staff members found</td>
                            @else
                            <td colspan="6" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No staff members found</td>
                            @endif
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Footer -->
        <div class="card-footer py-2 px-2 bg-light">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <!-- Per Page Dropdown -->
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 0.65rem;">Show:</span>
                    <select id="perPageSelect" class="custom-select-sm" style="font-size: 0.65rem; padding: 0.2rem 1.2rem 0.2rem 0.4rem; border: 1px solid #E8E2D5; border-radius: 4px; background: white;">
                        <option value="10" {{ $staffMembers->perPage() == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $staffMembers->perPage() == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $staffMembers->perPage() == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $staffMembers->perPage() == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="text-muted" style="font-size: 0.65rem;">per page</span>
                </div>

                <!-- Custom Pagination Links -->
                @if ($staffMembers->hasPages())
                <div class="custom-pagination">
                    <div class="pagination-links">
                        {{-- First Page --}}
                        @if ($staffMembers->onFirstPage())
                            <span class="pagination-item disabled">«</span>
                        @else
                            <a href="{{ $staffMembers->url(1) }}" class="pagination-item" data-page="1">«</a>
                        @endif

                        {{-- Previous Page --}}
                        @if ($staffMembers->onFirstPage())
                            <span class="pagination-item disabled">‹</span>
                        @else
                            <a href="{{ $staffMembers->previousPageUrl() }}" class="pagination-item" data-page="{{ $staffMembers->currentPage() - 1 }}">‹</a>
                        @endif

                        {{-- Page Numbers --}}
                        @php
                            $currentPage = $staffMembers->currentPage();
                            $lastPage = $staffMembers->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);

                            if ($start > 1) {
                                echo '<a href="' . $staffMembers->url(1) . '" class="pagination-item" data-page="1">1</a>';
                                if ($start > 2) echo '<span class="pagination-item dots">...</span>';
                            }

                            for ($i = $start; $i <= $end; $i++) {
                                if ($i == $currentPage) {
                                    echo '<span class="pagination-item active">' . $i . '</span>';
                                } else {
                                    echo '<a href="' . $staffMembers->url($i) . '" class="pagination-item" data-page="' . $i . '">' . $i . '</a>';
                                }
                            }

                            if ($end < $lastPage) {
                                if ($end < $lastPage - 1) echo '<span class="pagination-item dots">...</span>';
                                echo '<a href="' . $staffMembers->url($lastPage) . '" class="pagination-item" data-page="' . $lastPage . '">' . $lastPage . '</a>';
                            }
                        @endphp

                        {{-- Next Page --}}
                        @if ($staffMembers->hasMorePages())
                            <a href="{{ $staffMembers->nextPageUrl() }}" class="pagination-item" data-page="{{ $staffMembers->currentPage() + 1 }}">›</a>
                        @else
                            <span class="pagination-item disabled">›</span>
                        @endif

                        {{-- Last Page --}}
                        @if ($staffMembers->hasMorePages())
                            <a href="{{ $staffMembers->url($staffMembers->lastPage()) }}" class="pagination-item" data-page="{{ $staffMembers->lastPage() }}">»</a>
                        @else
                            <span class="pagination-item disabled">»</span>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Results Info -->
                <div class="text-muted" style="font-size: 0.6rem;">
                    Page {{ $staffMembers->currentPage() }} of {{ $staffMembers->lastPage() }}
                </div>
            </div>
        </div>
    </div>
