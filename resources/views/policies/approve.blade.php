@extends('layouts.master')

@section('title', 'Approve Policy')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'University Policies', 'url' => route('policies.index')],
        ['name' => $policy->title, 'url' => route('policies.show', $policy)],
        ['name' => 'Approve', 'url' => '#']
    ]" />

    <x-page-header title="APPROVE POLICY" description="Capture meeting reference, effective date, and chairperson/secretary signatures" backUrl="{{ route('policies.show', $policy) }}">
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('policies.approve.store', $policy) }}" id="approvePolicyForm">
                @csrf
                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light">
                        <span style="font-size: 0.7rem; font-weight: 600;">Approval Details</span>
                    </div>
                    <div class="card-body py-2 px-3" style="font-size: 0.75rem;">
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="font-size: 0.65rem;">Meeting Reference <span class="text-danger">*</span></label>
                                <input type="text" name="meeting_reference" value="{{ old('meeting_reference', $policy->meeting_reference ?? '') }}" class="form-control form-control-sm rounded-sm @error('meeting_reference') is-invalid @enderror" placeholder="e.g. Senate Meeting No. 5/2026" required>
                                @error('meeting_reference')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" style="font-size: 0.65rem;">Effective Date <span class="text-danger">*</span></label>
                                <input type="date" name="effective_date" value="{{ old('effective_date', optional($policy->effective_date ?? null)->format('Y-m-d')) }}" class="form-control form-control-sm rounded-sm @error('effective_date') is-invalid @enderror" required>
                                @error('effective_date')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" style="font-size: 0.65rem;">Review Due Date</label>
                                <input type="date" name="review_due_date" value="{{ old('review_due_date', optional($policy->review_due_date ?? null)->format('Y-m-d')) }}" class="form-control form-control-sm rounded-sm @error('review_due_date') is-invalid @enderror">
                                @error('review_due_date')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="font-size: 0.65rem;">Chairperson Name <span class="text-danger">*</span></label>
                                <input type="text" name="chairperson_name" value="{{ old('chairperson_name', $policy->chairperson_name ?? '') }}" class="form-control form-control-sm rounded-sm @error('chairperson_name') is-invalid @enderror" required>
                                @error('chairperson_name')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" style="font-size: 0.65rem;">Secretary Name <span class="text-danger">*</span></label>
                                <input type="text" name="secretary_name" value="{{ old('secretary_name', $policy->secretary_name ?? '') }}" class="form-control form-control-sm rounded-sm @error('secretary_name') is-invalid @enderror" required>
                                @error('secretary_name')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card rounded-sm mb-3">
                    <div class="card-header py-1 px-2 bg-light">
                        <span style="font-size: 0.7rem; font-weight: 600;">Signatures</span>
                    </div>
                    <div class="card-body py-2 px-3">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <x-signature-pad name="chairperson_signature_data" id="chairperson-signature" label="Chairperson Signature" helpText="Signed by the chairperson of the approving meeting." />
                            </div>
                            <div class="col-md-6">
                                <x-signature-pad name="secretary_signature_data" id="secretary-signature" label="Secretary Signature" helpText="Signed by the secretary of the approving meeting." />
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-sm rounded-sm">Approve Policy</button>
                <a href="{{ route('policies.show', $policy) }}" class="btn btn-secondary btn-sm rounded-sm">Cancel</a>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <span style="font-size: 0.7rem; font-weight: 600;">Policy Summary</span>
                </div>
                <div class="card-body py-2 px-3" style="font-size: 0.7rem;">
                    <p class="mb-1"><strong>Code:</strong> {{ $policy->code }}</p>
                    <p class="mb-1"><strong>Title:</strong> {{ $policy->title }}</p>
                    <p class="mb-1"><strong>Category:</strong> {{ $policy->categoryLabel() }}</p>
                    <p class="mb-0"><strong>Current Status:</strong> {{ ucfirst($policy->status) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
