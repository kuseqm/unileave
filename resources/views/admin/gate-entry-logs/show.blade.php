@extends('layouts.master')

@section('title', 'Gate Log Details')

@section('content')
<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Gate Entry Logs', 'url' => route('admin.gate-entry-logs.index')],
        ['name' => 'Log #' . $gateEntryLog->id, 'url' => route('admin.gate-entry-logs.show', $gateEntryLog)]
    ]" />

    <x-page-header title="GATE LOG #{{ $gateEntryLog->id }}" description="Details of the recorded entry or exit.">
        <a href="{{ route('admin.gate-entry-logs.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm">Back</a>
    </x-page-header>

    <div class="card border-0 rounded-sm shadow-sm">
        <div class="card-body p-3">
            <table class="table table-sm table-borderless" style="font-size: 0.75rem;">
                <tr>
                    <th style="width: 30%;">Staff</th>
                    <td>{{ optional($gateEntryLog->staff)->first_name }} {{ optional($gateEntryLog->staff)->last_name }}</td>
                </tr>
                <tr>
                    <th>Staff ID</th>
                    <td>{{ optional($gateEntryLog->staff)->staff_id }}</td>
                </tr>
                <tr>
                    <th>Action</th>
                    <td><span class="badge bg-{{ $gateEntryLog->action == 'entry' ? 'success' : 'warning' }}">{{ ucfirst($gateEntryLog->action) }}</span></td>
                </tr>
                <tr>
                    <th>Gate Name</th>
                    <td>{{ $gateEntryLog->gate_name }}</td>
                </tr>
                <tr>
                    <th>Verification Method</th>
                    <td>{{ ucfirst(str_replace('_', ' ', $gateEntryLog->verification_method)) }}</td>
                </tr>
                <tr>
                    <th>Verification Reference</th>
                    <td>{{ $gateEntryLog->verification_reference }}</td>
                </tr>
                <tr>
                    <th>Notes</th>
                    <td>{{ $gateEntryLog->notes }}</td>
                </tr>
                <tr>
                    <th>Recorded At</th>
                    <td>{{ optional($gateEntryLog->recorded_at)->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Recorded By</th>
                    <td>{{ optional($gateEntryLog->recorder)->first_name }} {{ optional($gateEntryLog->recorder)->last_name }}</td>
                </tr>
                <tr>
                    <th>Created</th>
                    <td>{{ $gateEntryLog->created_at->format('d M Y H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
