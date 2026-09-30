    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                <thead class="table-light">
                    <tr>
                        <th>Ref Number</th>
                        <th>Date Received</th>
                        <th>Sender</th>
                        <th>Office</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($letters as $letter)
                    <tr>
                        <td><strong>{{ $letter->ref_number }}</strong></td>
                        <td>{{ $letter->date_received->format('d/m/Y') }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($letter->sender, 25) }}</td>
                        <td>{{ $letter->office->short_name ?? $letter->office->name }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($letter->subject, 35) }}</td>
                        <td>
                            @if($letter->status == 'received')
                                <span class="badge bg-warning-subtle text-warning rounded-sm" style="font-size: 0.6rem;">Received</span>
                            @elseif($letter->status == 'assigned')
                                <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;">Assigned</span>
                            @elseif($letter->status == 'closed')
                                <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">Closed</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">{{ ucfirst($letter->status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($letter->priority == 'urgent')
                                <span class="badge bg-danger-subtle text-danger rounded-sm" style="font-size: 0.6rem;">Urgent</span>
                            @elseif($letter->priority == 'confidential')
                                <span class="badge bg-dark-subtle text-dark rounded-sm" style="font-size: 0.6rem;">Confidential</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.6rem;">Normal</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('registry.incoming.show', $letter->id) }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">
                                <i class="ti ti-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No incoming letters found. <a href="{{ route('registry.incoming.create') }}">Create one</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer py-1 px-2 bg-light">
        <x-pagination :items="$letters" :perPageOptions="[15, 25, 50, 100]" :ajax="true" />
    </div>
