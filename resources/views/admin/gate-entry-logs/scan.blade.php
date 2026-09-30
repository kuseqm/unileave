@extends('layouts.master')

@section('title', 'Scan Gate Pass')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Gate Entry Logs', 'url' => route('admin.gate-entry-logs.index')],
        ['name' => 'Scan', 'url' => route('admin.gate-entry-logs.scan')]
    ]" />

    <x-page-header title="SCAN GATE PASS" description="Scan a QR code, or enter a staff ID or email to verify and record entry/exit.">
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 rounded-sm shadow-sm mb-3">
                <div class="card-body p-3">
                    <form method="POST" action="{{ route('admin.gate-entry-logs.verify') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" style="font-size: 0.7rem;">Reference (QR code, Staff ID or Email)</label>
                            <input type="text" name="reference" class="form-control form-control-sm rounded-sm" value="{{ old('reference') }}" required autofocus>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.gate-entry-logs.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm">Back</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm">Verify</button>
                        </div>
                    </form>
                </div>
            </div>

            @isset($staff)
            <div class="card border-0 rounded-sm shadow-sm border-start border-4 border-success">
                <div class="card-body p-3">
                    <h6 class="card-title" style="font-size: 0.8rem;">Verified Person</h6>
                    <p class="mb-1"><strong>{{ $staff->title }} {{ $staff->first_name }} {{ $staff->middle_name }} {{ $staff->last_name }}</strong></p>
                    <p class="mb-1" style="font-size: 0.7rem;">Staff ID: {{ $staff->staff_id }} | Email: {{ $staff->email }}</p>
                    <p class="mb-1" style="font-size: 0.7rem;">Department: {{ optional($staff->department)->name }}</p>
                    <p class="mb-3" style="font-size: 0.7rem;">Reference: {{ $reference }} ({{ $context }})</p>

                    @can('record_entry_exit')
                    <form method="POST" action="{{ route('admin.gate-entry-logs.store') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $staff->id }}">
                        <input type="hidden" name="staff_id" value="{{ $staff->staff_id }}">
                        <input type="hidden" name="verification_method" value="qr_code">
                        <input type="hidden" name="verification_reference" value="{{ $reference }}">

                        <div class="row g-2">
                            <div class="col-4">
                                <select name="action" class="form-select form-select-sm rounded-sm" required>
                                    <option value="entry">Entry</option>
                                    <option value="exit">Exit</option>
                                </select>
                            </div>
                            <div class="col-5">
                                <input type="text" name="gate_name" class="form-control form-control-sm rounded-sm" placeholder="Gate name" required>
                            </div>
                            <div class="col-3">
                                <button type="submit" class="btn btn-success btn-sm rounded-sm w-100">Record</button>
                            </div>
                        </div>
                    </form>
                    @endcan
                </div>
            </div>
            @endisset
        </div>
    </div>
</div>
@endsection
