@extends('layouts.master')

@section('title', 'Letter Signatories')

@section('content')
<div class="container-fluid px-2">

    <x-page-header title="LETTER SIGNATORIES" description="Who can sign a verification letter - their office name heads the letter and their title closes it">
        <a href="{{ route('verification-letters.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Letters
        </a>
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm mb-3">
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle" style="font-size: 0.72rem;">
                        <thead class="table-light"><tr><th>Name</th><th>Title (English)</th><th>Letterhead office</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            @forelse($signatories as $signatory)
                            <tr>
                                <td class="fw-semibold">{{ $signatory->name }}<div class="text-muted fw-normal" style="font-size: 0.62rem;">{{ $signatory->title_sw }}</div></td>
                                <td>{{ $signatory->title_en }}@if($signatory->unit_en)<div class="text-muted" style="font-size: 0.62rem;">{{ $signatory->unit_en }}</div>@endif</td>
                                <td>{{ $signatory->office_en }}<div class="text-muted" style="font-size: 0.62rem;">{{ $signatory->phone }} {{ $signatory->email }}</div></td>
                                <td>@if($signatory->is_active)<span class="badge bg-success-subtle text-success rounded-sm">Active</span>@else<span class="badge bg-light text-muted border rounded-sm">Hidden</span>@endif</td>
                                <td class="text-end">
                                    <form action="{{ route('verification-letters.signatories.toggle', $signatory->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.62rem; padding: 0.1rem 0.4rem;">{{ $signatory->is_active ? 'Hide' : 'Show' }}</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No signatories yet - add one on the right.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-plus text-primary me-1"></i>Add a signatory</span></div>
                <div class="card-body p-3">
                    <form action="{{ route('verification-letters.signatories.store') }}" method="POST">
                        @csrf
                        @php
                            $fields = [
                                ['name', 'Name as it should print', 'Professor Valence M. Kessy Ndesendo, PhD', true],
                                ['title_en', 'Title (English)', 'Deputy Vice Chancellor (Academic)', true],
                                ['unit_en', 'Second line (English)', "St John’s University of Tanzania", false],
                                ['title_sw', 'Title (Swahili)', 'Naibu Makamu Mkuu wa Chuo (Taaluma)', true],
                                ['unit_sw', 'Second line (Swahili)', 'Chuo Kikuu cha St John’s cha Tanzania', false],
                                ['office_en', 'Office name on the letterhead', 'Office of the Deputy Vice Chancellor (Academic)', true],
                                ['phone', 'Phone on the letterhead', '+255 67 70 86094', false],
                                ['email', 'Email on the letterhead', 'dvca@sjut.ac.tz', false],
                            ];
                        @endphp
                        @foreach($fields as [$field, $label, $placeholder, $required])
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
                            <input type="text" name="{{ $field }}" value="{{ old($field) }}" class="form-control form-control-sm rounded-sm @error($field) is-invalid @enderror" placeholder="{{ $placeholder }}" @required($required)>
                            @error($field)<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                        </div>
                        @endforeach
                        <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;"><i class="ti ti-plus me-1"></i> Add Signatory</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
