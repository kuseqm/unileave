@extends('layouts.master')

@section('title', 'Leave Approval Chain')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'Leave Approval Chain', 'url' => route('admin.leave-approval-chain.index')]
    ]" />

    <x-page-header title="LEAVE APPROVAL CHAIN" description="Reorder the review stages a leave request passes through. Changes apply immediately to new submissions and to requests currently mid-chain." />

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2">
        <!-- Main Column -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-arrows-shuffle text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Approval Chain Order</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" style="font-size: 0.8rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-2" style="width: 60px;">Order</th>
                                    <th>Stage</th>
                                    <th class="pe-2" style="width: 140px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stages as $stage)
                                <tr>
                                    <td class="ps-2 text-muted">{{ $stage->sequence_order }}</td>
                                    <td>
                                        <strong>{{ $stage->display_name }}</strong>
                                        @if($stage->stage_key === 'hr_logistics')
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1" style="font-size: 0.6rem;">Fixed - always last</span>
                                        @endif
                                    </td>
                                    <td class="pe-2">
                                        @if($stage->stage_key !== 'hr_logistics')
                                        <form method="POST" action="{{ route('admin.leave-approval-chain.move-up', $stage->stage_key) }}" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;" title="Move up">
                                                <i class="ti ti-arrow-up"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.leave-approval-chain.move-down', $stage->stage_key) }}" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;" title="Move down">
                                                <i class="ti ti-arrow-down"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
                            <i class="ti ti-arrows-shuffle text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">How This Works</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">The four stages can be reordered freely using the arrows. Only the <strong>order</strong> they run in changes.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-user-check text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Who Approves Doesn't Change</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Each stage keeps its own rules for exactly who approves - the applicant's own Head of Department, their school's Dean, DVC Academic or DVC PFA depending on staff category.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-flag text-danger mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">HR Logistics Always Runs Last</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">It finalizes the request - signature, letter, balance deduction - and isn't part of the reorderable list.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-clock-bolt text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">When Changes Take Effect</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Immediately - for new submissions and for requests already mid-chain, which continue through whatever stages come next in the new order.</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
