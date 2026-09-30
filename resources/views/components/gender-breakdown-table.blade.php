@props([
    'rows',
    'heading' => 'Group',
    'maxHeight' => null,
])

{{--
    One breakdown of graduates: a label, then Males / Females / Total and the
    female share. $rows are objects with label, sub (optional small line),
    males, females, total - as produced by StudentStatisticsController.
--}}
@php
    $grandTotal = (int) $rows->sum('total');
@endphp
<div class="table-responsive" @if($maxHeight) style="max-height: {{ $maxHeight }}; overflow-y: auto;" @endif>
    <table class="table table-sm mb-0 align-middle" style="font-size: 0.7rem;">
        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
            <tr>
                <th>{{ $heading }}</th>
                <th class="text-end">Males</th>
                <th class="text-end">Females</th>
                <th class="text-end">Total</th>
                <th style="min-width: 90px;">Female share</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            @php($share = $row->total > 0 ? round(($row->females / $row->total) * 100) : 0)
            <tr>
                <td>
                    <span class="fw-semibold">{{ $row->label }}</span>
                    @if(! empty($row->sub))<div class="text-muted" style="font-size: 0.6rem;">{{ $row->sub }}</div>@endif
                </td>
                <td class="text-end">{{ number_format($row->males) }}</td>
                <td class="text-end">{{ number_format($row->females) }}</td>
                <td class="text-end fw-semibold">{{ number_format($row->total) }}</td>
                <td>
                    <div class="d-flex align-items-center gap-1">
                        <div class="progress flex-grow-1" style="height: 5px;" role="progressbar" aria-valuenow="{{ $share }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: {{ $share }}%; background-color: #B88F42;"></div>
                        </div>
                        <span class="text-muted" style="font-size: 0.6rem; width: 2rem; text-align: right;">{{ $share }}%</span>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-3">No graduates match.</td></tr>
            @endforelse
        </tbody>
        @if($rows->count() > 1)
        <tfoot class="table-light">
            <tr class="fw-semibold">
                <td>All</td>
                <td class="text-end">{{ number_format($rows->sum('males')) }}</td>
                <td class="text-end">{{ number_format($rows->sum('females')) }}</td>
                <td class="text-end">{{ number_format($grandTotal) }}</td>
                <td class="text-muted" style="font-size: 0.6rem;">{{ $grandTotal > 0 ? round(($rows->sum('females') / $grandTotal) * 100) : 0 }}%</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
