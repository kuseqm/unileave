    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                <thead class="table-light">
                    <tr>
                        <th>Ref Number</th>
                        <th>Recipient</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Dispatched Via</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($letters as $letter)
                    <tr>
                        <td><strong>{{ $letter->ref_number }}</strong></td>
                        <td>{{ \Illuminate\Support\Str::limit($letter->recipient, 25) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($letter->subject, 30) }}</td>
                        <td>
                            @if($letter->status == 'draft')
                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Draft</span>
                            @elseif($letter->status == 'pending_approval')
                                <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Pending</span>
                            @elseif($letter->status == 'approved')
                                <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">Approved</span>
                            @elseif($letter->status == 'dispatched')
                                <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Dispatched</span>
                            @endif
                        </td>
                        <td>
                            @if($letter->dispatched_via == 'email')
                                <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.6rem;">Email</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Print</span>
                            @endif
                        </td>
                        <td>{{ $letter->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('registry.outgoing.show', $letter->id) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">
                                <i class="ti ti-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No outgoing letters found. <a href="{{ route('registry.outgoing.create') }}">Create one</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer py-1 px-2 bg-light">
        <x-pagination :items="$letters" :perPageOptions="[15, 25, 50, 100]" :ajax="true" />
    </div>
