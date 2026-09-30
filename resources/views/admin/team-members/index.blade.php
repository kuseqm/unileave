@extends('layouts.master')

@section('title', 'Team Members')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Team Members', 'url' => route('admin.team-members.index')]
    ]" />

    <x-page-header title="TEAM MEMBERS" description="The 'Built by' section on the public landing page.">
        <a href="{{ route('admin.team-members.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> New Team Member
        </a>
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2">
        <!-- Main Column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-users text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Team Members</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" style="font-size: 0.75rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-2" style="width: 60px;">Order</th>
                                    <th style="width: 60px;">Photo</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th style="width: 90px;">Status</th>
                                    <th class="pe-2" style="width: 130px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($members as $m)
                                <tr>
                                    <td class="ps-2">
                                        <div class="d-flex flex-column gap-1">
                                            <form method="POST" action="{{ route('admin.team-members.move-up', $m->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;" title="Move up">
                                                    <i class="ti ti-chevron-up"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.team-members.move-down', $m->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.6rem; padding: 0.1rem 0.3rem;" title="Move down">
                                                    <i class="ti ti-chevron-down"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    <td>
                                        @if($m->photo_url)
                                            <img src="{{ $m->photo_url }}" alt="{{ $m->name }}" style="width: 36px; height: 36px; object-fit: cover; border-radius: 50%;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary" style="width: 36px; height: 36px; border-radius: 50%; font-size: 0.65rem;">
                                                {{ strtoupper(substr($m->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td><strong>{{ $m->name }}</strong></td>
                                    <td class="text-muted">{{ $m->role }}</td>
                                    <td>
                                        <span class="badge bg-{{ $m->is_active ? 'success' : 'secondary' }}-subtle text-{{ $m->is_active ? 'success' : 'secondary' }} rounded-pill">
                                            {{ $m->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="pe-2">
                                        <a href="{{ route('admin.team-members.edit', $m->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.team-members.destroy', $m->id) }}" class="d-inline" onsubmit="return confirm('Remove this team member?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No team members yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-1 px-2">
                    <x-pagination :items="$members" />
                </div>
            </div>
        </div>

        <!-- Info Column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-photo text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Photo Is Optional</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">A member without a photo shows an initial-letter avatar instead - no need to have one on file to add someone to the list.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-arrows-vertical text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Display Order</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">The up/down arrows control the order members appear in on the landing page's "Built by" section.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-eye-off text-secondary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Inactive Members Stay Hidden</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Marking someone Inactive keeps their record here for later, without removing them or showing them publicly.</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
