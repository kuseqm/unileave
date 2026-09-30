@extends('layouts.master')

@section('title', 'New Approval Delegation')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Approval Delegations', 'url' => route('admin.approval-delegations.index')],
        ['name' => 'New Delegation', 'url' => route('admin.approval-delegations.create')]
    ]" />

    <x-page-header title="NEW APPROVAL DELEGATION" description="Assign a temporary approval authority to another staff member.">
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2">
        <!-- Form - main column -->
        <div class="col-lg-7">
            <div class="card border-0 rounded-sm shadow-sm">
                <div class="card-body p-2">
                    <form method="POST" action="{{ route('admin.approval-delegations.store') }}">
                        @csrf
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.7rem;">Delegator</label>
                                <select name="delegator_staff_id" class="form-select form-select-sm rounded-sm @error('delegator_staff_id') is-invalid @enderror" style="font-size: 0.7rem;" required>
                                    <option value="">Select delegator</option>
                                    @foreach($delegators as $user)
                                    <option value="{{ $user->id }}" {{ old('delegator_staff_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name }} ({{ $user->getRoleNames()->first() ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                                @error('delegator_staff_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.7rem;">Delegate</label>
                                <select name="delegate_staff_id" class="form-select form-select-sm rounded-sm @error('delegate_staff_id') is-invalid @enderror" style="font-size: 0.7rem;" required>
                                    <option value="">Select delegate</option>
                                    @foreach($staff as $user)
                                    <option value="{{ $user->id }}" {{ old('delegate_staff_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('delegate_staff_id')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.7rem;">Approval Level</label>
                                <select name="level" class="form-select form-select-sm rounded-sm @error('level') is-invalid @enderror" style="font-size: 0.7rem;" required>
                                    @foreach(['hod' => 'HOD', 'dean' => 'Dean', 'dvc' => 'DVC', 'hr' => 'HR', 'hr_logistics' => 'HR Logistics'] as $key => $label)
                                    <option value="{{ $key }}" {{ old('level', 'hod') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('level')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.7rem;">Start Date</label>
                                <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control form-control-sm rounded-sm @error('start_date') is-invalid @enderror" style="font-size: 0.7rem;" required>
                                @error('start_date')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.7rem;">End Date</label>
                                <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control form-control-sm rounded-sm @error('end_date') is-invalid @enderror" style="font-size: 0.7rem;" required>
                                @error('end_date')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size: 0.7rem;">Reason</label>
                            <textarea name="reason" rows="3" class="form-control form-control-sm rounded-sm @error('reason') is-invalid @enderror" style="font-size: 0.7rem;">{{ old('reason') }}</textarea>
                            @error('reason')
                            <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('admin.approval-delegations.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">Save Delegation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Information - narrow column -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">About Delegations</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <small class="text-muted" style="font-size: 0.65rem;">A delegation lets one staff member temporarily approve on behalf of another at a specific level (HOD, Dean, DVC, HR, or HR Logistics) - useful when the real approver is away.</small>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Note</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">A delegate can never approve their own request, even during an active delegation window.</small>
                            </div>

</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
