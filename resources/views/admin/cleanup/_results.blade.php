@php $moduleColors = ['leave' => 'primary', 'permission' => 'warning', 'travel' => 'secondary']; @endphp

@if($items->count() > 0)
<div class="table-responsive">
    <table class="table table-sm mb-0" style="font-size: 0.75rem;">
        <thead class="bg-light">
            <tr>
                <th class="ps-2 py-1" style="width: 30px;"><input type="checkbox" id="selectAll"></th>
                <th class="py-1">Module</th>
                <th class="py-1">Applicant</th>
                <th class="py-1">Detail</th>
                <th class="py-1">Status</th>
                <th class="py-1">Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td class="ps-2 py-1">
                    <input type="checkbox" class="row-check" name="items[]" value="{{ $item['module'] }}:{{ $item['id'] }}">
                </td>
                <td class="py-1">
                    <span class="badge bg-{{ $moduleColors[$item['module']] }}-subtle text-{{ $moduleColors[$item['module']] }} rounded-sm">{{ ucfirst($item['module']) }}</span>
                </td>
                <td class="py-1 fw-semibold">{{ $item['applicant'] }}</td>
                <td class="py-1 text-muted">{{ $item['detail'] }}</td>
                <td class="py-1">{{ ucfirst($item['status']) }}</td>
                <td class="py-1 text-muted">{{ $item['created_at']->format('d M Y, H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="text-center py-4 text-muted">
    <i class="ti ti-circle-check mb-2 d-block" style="font-size: 1.5rem;"></i>
    <p class="mb-0" style="font-size: 0.75rem;">No records match this filter.</p>
</div>
@endif
