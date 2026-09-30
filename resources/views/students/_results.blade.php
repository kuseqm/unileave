    <!-- Students Table -->
    <div class="card rounded-sm">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-school text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Graduate Records</span>
                <span class="text-muted ms-2 small">Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2 py-1">Reg. Number</th>
                            <th class="py-1">Full Name</th>
                            <th class="py-1">Programme</th>
                            <th class="py-1">Grad. Year</th>
                            <th class="py-1">Status</th>
                            <th class="pe-2 py-1 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td class="ps-2 py-1"><code style="font-size: 0.65rem;">{{ $student->registration_number }}</code></td>
                            <td class="py-1">{{ $student->formatted_name }}</td>
                            <td class="py-1" @if($student->program) title="{{ $student->program->name }}" @endif>{{ $student->program->code ?? $student->program->name ?? '—' }}</td>
                            <td class="py-1">{{ $student->graduation_year ?? '—' }}</td>
                            <td class="py-1">
                                @if($student->is_graduated)
                                    <span class="badge bg-success rounded-sm" style="font-size: 0.6rem;"><i class="ti ti-check"></i> Graduated</span>
                                @else
                                    <span class="badge bg-secondary rounded-sm" style="font-size: 0.6rem;">Not Graduated</span>
                                @endif
                            </td>
                            <td class="pe-2 py-1 text-end">
                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-outline-info rounded-sm" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;" title="View">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted" style="font-size: 0.7rem;">
                                <i class="ti ti-school" style="font-size: 48px;"></i>
                                <p class="mt-2">No records found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1 px-2">
            <x-pagination :items="$students" :perPageOptions="[10, 25, 50, 100]" :ajax="true" />
        </div>
    </div>
