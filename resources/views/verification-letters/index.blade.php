@extends('layouts.master')

@section('title', 'Verification Letters')

@section('content')
<div class="container-fluid px-2">

    <x-page-header title="VERIFICATION LETTERS" description="Letters confirming to an employer, agency or ministry that a certificate and transcript are authentic">
        @can('prepare_verification_letters')
        <a href="{{ route('verification-letters.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-plus me-1"></i> New Letter
        </a>
        @endcan
        @can('manage_letter_signatories')
        <a href="{{ route('verification-letters.signatories') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-signature me-1"></i> Signatories
        </a>
        @endcan
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Graduates
        </a>
    </x-page-header>

    <x-alert-success />
    <x-alert-danger />

    <div class="card rounded-sm mb-2">
        <div class="card-body p-2">
            <form method="GET" action="{{ route('verification-letters.index') }}" class="d-flex gap-1">
                <input type="search" name="search" value="{{ request('search') }}" class="form-control form-control-sm rounded-sm" style="font-size: 0.72rem;" placeholder="Search by reference number, who asked, their reference, or a graduate's name / number...">
                <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;"><i class="ti ti-search"></i></button>
                @if(request('search'))<a href="{{ route('verification-letters.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Reset</a>@endif
            </form>
        </div>
    </div>

    <div class="card rounded-sm mb-3">
        <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle" style="font-size: 0.72rem;">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>Date</th>
                        <th>Addressed to</th>
                        <th>Graduates</th>
                        <th>Signed by</th>
                        <th>Kind</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($letters as $letter)
                    <tr>
                        <td><code>{{ $letter->letter_number }}</code></td>
                        <td>{{ $letter->letter_date->format('d M Y') }}</td>
                        <td>
                            <div class="fw-semibold">{{ $letter->recipient_organisation }}</div>
                            @if($letter->received_reference)<small class="text-muted">Their ref: {{ $letter->received_reference }}</small>@endif
                        </td>
                        <td>
                            @foreach($letter->items->take(2) as $item)<div>{{ $item->name }} <span class="text-muted">{{ $item->number }}</span></div>@endforeach
                            @if($letter->items->count() > 2)<small class="text-muted">and {{ $letter->items->count() - 2 }} more</small>@endif
                        </td>
                        <td>{{ $letter->signatory->name ?? '—' }}<div class="text-muted" style="font-size: 0.62rem;">{{ $letter->signatory->title_en ?? '' }}</div></td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary rounded-sm">{{ \App\Models\VerificationLetter::LANGUAGES[$letter->language] }}</span>
                            @if($letter->template === 'dataflow')<span class="badge bg-warning-subtle text-warning-emphasis rounded-sm">DataFlow</span>@endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('verification-letters.show', $letter->id) }}" class="btn btn-outline-info btn-sm rounded-sm" style="font-size: 0.62rem; padding: 0.1rem 0.4rem;"><i class="ti ti-eye"></i> Open</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No verification letters yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-top">
            <x-pagination :items="$letters" :perPageOptions="[10, 25, 50]" />
        </div>
    </div>
</div>
@endsection
