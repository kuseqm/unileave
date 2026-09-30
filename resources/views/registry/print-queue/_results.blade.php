    <div class="card-body p-0">
        <form method="POST" action="{{ route('registry.print-queue.batch-print') }}" id="batchPrintForm">
            @csrf
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1" width="30"><input type="checkbox" id="selectAll"></th>
                            <th class="py-1">Letter Ref</th>
                            <th class="py-1">Recipient</th>
                            <th class="py-1">Created</th>
                            <th class="pe-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($queue as $item)
                        <tr>
                            <td class="ps-2 py-1"><input type="checkbox" name="ids[]" value="{{ $item->id }}" class="job-checkbox"></td>
                            <td class="py-1"><strong>{{ $item->outgoingLetter->ref_number ?? 'N/A' }}</strong></td>
                            <td class="py-1">{{ $item->recipient_address }}</td>
                            <td class="py-1">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td class="pe-2 py-1">
                                <button type="button" class="btn btn-sm btn-primary rounded-sm" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;" onclick="printSingle({{ $item->id }})">Print</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted" style="font-size: 0.7rem;">No pending print jobs.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($queue->count() > 0)
            <div class="p-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Batch Print Selected</button>
            </div>
            @endif
        </form>
    </div>
    <div class="card-footer py-1 px-2">
        <x-pagination :items="$queue" :perPageOptions="[20, 50, 100]" :ajax="true" />
    </div>
