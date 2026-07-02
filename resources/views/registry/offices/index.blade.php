@extends('layouts.app')

@section('title', 'Manage Offices')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Manage Offices</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('registry.offices.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    New Office
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Short Name</th>
                                <th>Department</th>
                                <th>Receives Letters</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($offices as $office)
                            <tr>
                                <td><strong>{{ $office->name }}</strong></td>
                                <td>{{ $office->short_name }}</td>
                                <td>{{ $office->department->name ?? 'Central' }}</td>
                                <td>
                                    @if($office->receives_letters)
                                    <span class="badge bg-green">Yes</span>
                                    @else
                                    <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($office->is_active)
                                    <span class="badge bg-green">Active</span>
                                    @else
                                    <span class="badge bg-red">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('registry.offices.edit', $office->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteOffice({{ $office->id }})">Delete</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No offices found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $offices->links() }}
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteOffice(id) {
    if (confirm('Are you sure you want to delete this office?')) {
        const form = document.getElementById('deleteForm');
        form.action = `{{ url('registry/offices') }}/${id}`;
        form.submit();
    }
}
</script>
@endsection