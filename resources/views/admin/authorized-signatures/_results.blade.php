    <!-- Signatures Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-signature text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Signatures List</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="50">SN</th>
                            <th class="py-1" width="120">Preview</th>
                            <th class="py-1">Name</th>
                            <th class="pe-2 py-1" width="80">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($signatures as $index => $signature)
                        <tr>
                            <td class="ps-2 py-1">{{ $signatures->firstItem() + $index }}</td>
                            <td class="py-1">
                                @if($signature->signature_path)
                                    <img src="{{ Storage::url($signature->signature_path) }}" alt="{{ $signature->name }}" style="height: 32px; max-width: 100px; object-fit: contain;">
                                @else
                                    <span class="text-muted" style="font-size: 0.65rem;">No image</span>
                                @endif
                            </td>
                            <td class="py-1 fw-semibold">{{ $signature->name }}</td>
                            <td class="pe-2 py-1">
                                <x-action-buttons
                                    editRoute="{{ route('admin.authorized-signatures.edit', $signature->id) }}"
                                    deleteRoute="{{ route('admin.authorized-signatures.destroy', $signature->id) }}"
                                    deleteId="{{ $signature->id }}"
                                    deleteName="{{ $signature->name }}"
                                    :showDelete="true"
                                    deleteType="authorized_signature"
                                />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                No authorized signatures found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <x-pagination :items="$signatures" :perPageOptions="[10, 25, 50, 100]" :ajax="true" />
        </div>
    </div>
