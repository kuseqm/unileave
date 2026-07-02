@extends('layouts.app')

@section('title', 'Print Queue')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Print Queue</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Pending Print Jobs -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Pending Print Jobs</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('registry.print-queue.batch-print') }}" id="batchPrintForm">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Letter Ref</th>
                                    <th>Recipient</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($queue as $item)
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="{{ $item->id }}" class="job-checkbox"></td>
                                    <td><strong>{{ $item->outgoingLetter->ref_number ?? 'N/A' }}</strong></td>
                                    <td>{{ $item->recipient_address }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="printSingle({{ $item->id }})">Print</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No pending print jobs.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($queue->count() > 0)
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Batch Print Selected</button>
                    </div>
                    @endif
                </form>
            </div>
            <div class="card-footer">
                {{ $queue->links() }}
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('selectAll')?.addEventListener('change', function(e) {
    document.querySelectorAll('.job-checkbox').forEach(cb => cb.checked = e.target.checked);
});

function printSingle(id) {
    fetch(`{{ url('registry/print-queue') }}/${id}/print`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => location.reload());
}
</script>
@endsection