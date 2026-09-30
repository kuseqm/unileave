@extends('layouts.master')

@section('title', 'Policy Versions')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'University Policies', 'url' => route('policies.index')],
        ['name' => $policy->title, 'url' => route('policies.show', $policy)],
        ['name' => 'Version History', 'url' => '#']
    ]" />

    <x-page-header title="VERSION HISTORY" description="{{ $policy->code }} — {{ $policy->title }}" backUrl="{{ route('policies.show', $policy) }}">
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2">
        <!-- Version History - main column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" style="font-size: 0.75rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Version</th>
                                <th>Status</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Effective</th>
                                <th>Meeting Ref</th>
                                <th>Signed</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($versions as $version)
                            <tr>
                                <td>v{{ $version->version }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($version->status) }}</span></td>
                                <td>{{ $version->title }}</td>
                                <td>{{ $version->categoryLabel() }}</td>
                                <td>{{ $version->effective_date?->format('d M Y') ?? '-' }}</td>
                                <td>{{ $version->meeting_reference ?? '-' }}</td>
                                <td>{{ $version->chairperson_name ? 'Yes' : 'No' }}</td>
                                <td>{{ $version->created_at?->format('d M Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">No versions found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-2">{{ $versions->links() }}</div>
            </div>
        </div>

        <!-- Information - narrow column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> About Versions</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>Each row is a distinct revision of this policy, in the order it was created</li>
                            <li><strong>Signed</strong> shows whether a chairperson name is recorded for that version</li>
                            <li>The <strong>Meeting Ref</strong> links the version to the meeting that approved it</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
