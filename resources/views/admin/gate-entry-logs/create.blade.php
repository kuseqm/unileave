@extends('layouts.master')

@section('title', 'Record Gate Entry / Exit')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Gate Entry Logs', 'url' => route('admin.gate-entry-logs.index')],
        ['name' => 'Manual Record', 'url' => route('admin.gate-entry-logs.create')]
    ]" />

    <x-page-header title="RECORD GATE ENTRY / EXIT" description="Manually record staff entry or exit at a gate.">
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 rounded-sm shadow-sm">
                <div class="card-body p-3">
                    <form method="POST" action="{{ route('admin.gate-entry-logs.store') }}">
                        @csrf

                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.7rem;">Staff ID or Email</label>
                            <input type="text" name="staff_id" class="form-control form-control-sm rounded-sm" value="{{ old('staff_id') }}" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.7rem;">Action</label>
                            <select name="action" class="form-select form-select-sm rounded-sm" required>
                                <option value="entry" {{ old('action') == 'entry' ? 'selected' : '' }}>Entry</option>
                                <option value="exit" {{ old('action') == 'exit' ? 'selected' : '' }}>Exit</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.7rem;">Gate Name</label>
                            <input type="text" name="gate_name" class="form-control form-control-sm rounded-sm" value="{{ old('gate_name') }}" placeholder="Main Gate" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.7rem;">Recorded At</label>
                            <input type="datetime-local" name="recorded_at" class="form-control form-control-sm rounded-sm" value="{{ old('recorded_at', now()->format('Y-m-d\TH:i')) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-size: 0.7rem;">Notes</label>
                            <textarea name="notes" class="form-control form-control-sm rounded-sm" rows="2">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.gate-entry-logs.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm">Save Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
