@extends('layouts.master')

@section('title', 'Build Your Own Report')

@section('content')
<div class="container-fluid px-2">

    <x-breadcrumb :items="[
        ['name' => 'Reports', 'url' => route('reports.index')],
        ['name' => 'Build Your Own Report', 'url' => route('reports.custom')]
    ]" />

    <x-page-header title="BUILD YOUR OWN REPORT" description="Pick a data source, choose exactly the columns you need, and combine any filters you like - instead of only the predefined reports." backUrl="{{ route('reports.index') }}" />

    <div class="row g-2">
        <!-- Main Column -->
        <div class="col-lg-8">
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-adjustments text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Report Builder</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form method="GET" action="{{ route('reports.custom') }}" id="customReportForm">
                        <!-- Step 1: Data Source -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">1. Data Source</label>
                            <select name="source" class="form-select form-select-sm rounded-sm" style="font-size: 0.75rem;" onchange="this.form.submit()">
                                <option value="">-- Choose what to report on --</option>
                                @foreach($sources as $key => $config)
                                    <option value="{{ $key }}" {{ $source === $key ? 'selected' : '' }}>{{ $config['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($source && isset($sources[$source]))
                        @php $config = $sources[$source]; @endphp

                        <!-- Step 2: Columns -->
                        <div class="mb-2 border-top pt-2">
                            <label class="form-label" style="font-size: 0.65rem;">2. Columns to Include</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($config['columns'] as $colKey => $colLabel)
                                <div class="form-check">
                                    <input type="checkbox" name="columns[]" value="{{ $colKey }}" class="form-check-input" id="col_{{ $colKey }}"
                                        {{ empty($columns) || in_array($colKey, $columns) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="col_{{ $colKey }}" style="font-size: 0.7rem;">{{ $colLabel }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Step 3: Filters -->
                        <div class="mb-2 border-top pt-2">
                            <label class="form-label" style="font-size: 0.65rem;">3. Filters (optional - leave blank to include everything)</label>
                            <div class="row g-1">
                                @if(in_array('department_id', $config['filters']))
                                <div class="col-md-6">
                                    <label class="form-label mb-0" style="font-size: 0.6rem;">Department</label>
                                    <select name="department_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                        <option value="">All departments</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                @if(in_array('leave_type_id', $config['filters']))
                                <div class="col-md-6">
                                    <label class="form-label mb-0" style="font-size: 0.6rem;">Leave Type</label>
                                    <select name="leave_type_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                        <option value="">All leave types</option>
                                        @foreach($leaveTypes as $type)
                                            <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                @if(in_array('status', $config['filters']))
                                <div class="col-md-6">
                                    <label class="form-label mb-0" style="font-size: 0.6rem;">Status</label>
                                    <select name="status" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                        <option value="">Any status</option>
                                        @foreach($config['statuses'] as $statusOption)
                                            <option value="{{ $statusOption }}" {{ request('status') == $statusOption ? 'selected' : '' }}>{{ ucfirst($statusOption) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                @if(in_array('date_from', $config['filters']))
                                <div class="col-md-3">
                                    <label class="form-label mb-0" style="font-size: 0.6rem;">From</label>
                                    <input type="date" name="date_from" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" value="{{ request('date_from') }}">
                                </div>
                                @endif

                                @if(in_array('date_to', $config['filters']))
                                <div class="col-md-3">
                                    <label class="form-label mb-0" style="font-size: 0.6rem;">To</label>
                                    <input type="date" name="date_to" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" value="{{ request('date_to') }}">
                                </div>
                                @endif
                            </div>
                        </div>

                        <input type="hidden" name="source" value="{{ $source }}">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-report me-1"></i> Generate Report
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            @if($reportData !== null)
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-table text-primary me-1" style="font-size: 0.8rem;"></i>
                            <span style="font-size: 0.7rem; font-weight: 600;">Results</span>
                        </div>
                        <div class="text-muted" style="font-size: 0.6rem;">
                            Showing {{ $reportData['rows']->count() }} records
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                            <thead class="bg-light">
                                <tr>
                                    @foreach($reportData['rows']->first() ?? [] as $col => $val)
                                        <th class="py-1">{{ ucwords(str_replace('_', ' ', $col)) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData['rows'] as $row)
                                <tr>
                                    @foreach($row as $value)
                                        <td class="py-1">{{ $value }}</td>
                                    @endforeach
                                </tr>
                                @empty
                                <tr><td colspan="100" class="text-center py-3 text-muted">No data matches this combination.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-1 px-2">
                    <small class="text-muted">Export options:
                        <a href="{{ route('reports.custom.export.pdf').'?'.http_build_query(request()->query()) }}" class="text-danger">PDF</a> |
                        <a href="{{ route('reports.custom.export.excel').'?'.http_build_query(request()->query()) }}" class="text-success">Excel</a>
                    </small>
                </div>
            </div>
            @endif
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
                            <i class="ti ti-list-numbers text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Three Simple Steps</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Choose a data source, tick the columns you want to see, add any filters, then Generate. Nothing is required except the source.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-checkbox text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Mix and Match Freely</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Unlike the predefined reports, you decide which columns appear and in what combination of filters - nothing is fixed in advance.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-link text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Shareable Link</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Your exact selection lives in the page's URL - copy it to re-open the same report later or send it to a colleague.</small>
                            </div>
                        </div>
                    </x-alert-light>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-file-export text-info mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Export Anytime</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Once results are showing, PDF and Excel exports below the table respect your exact column and filter choices.</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
