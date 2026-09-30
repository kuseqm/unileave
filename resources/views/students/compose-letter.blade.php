@extends('layouts.master')

@section('title', 'Compose Letter - ' . $student->formatted_name)

@section('content')
<div class="container-fluid px-2">

    <x-page-header title="COMPOSE GRADUATE LETTER" description="{{ $student->formatted_name }} ({{ $student->registration_number }})">
        <a href="{{ route('students.show', $student->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back to Graduate
        </a>
    </x-page-header>

    <!-- Letter Type Tabs -->
    <ul class="nav nav-tabs mb-3">
        @foreach($letterTypes as $key => $letterType)
        <li class="nav-item">
            <a class="nav-link {{ $type === $key ? 'active' : '' }}" href="{{ route('students.compose-letter', ['student' => $student->id, 'type' => $key]) }}" style="font-size: 0.75rem;">
                {{ $letterType['label'] }}
            </a>
        </li>
        @endforeach
    </ul>

    <div class="row g-2">
        <div class="col-lg-12">
            <form action="{{ route('graduation-letters.store', $student->id) }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">

                <div class="card rounded-sm mb-2">
                    <div class="card-header py-1 px-2 bg-light">
                        <span style="font-size: 0.7rem; font-weight: 600;">{{ $config['label'] }}</span>
                    </div>
                    <div class="card-body p-3">
                        @if($config['requires_recipient_input'])
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 0.65rem;">Recipient Name <span class="text-danger">*</span></label>
                                <input type="text" name="recipient_name" class="form-control form-control-sm rounded-sm @error('recipient_name') is-invalid @enderror" style="font-size: 0.7rem;" placeholder="e.g., EXECUTIVE SECRETARY" value="{{ old('recipient_name') }}" required>
                                @error('recipient_name')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" style="font-size: 0.65rem;">Recipient Address <span class="text-danger">*</span></label>
                                <textarea name="recipient_address" rows="1" class="form-control form-control-sm rounded-sm @error('recipient_address') is-invalid @enderror" style="font-size: 0.7rem;" placeholder="One line per address line, e.g. organisation, P.O. Box, town" required>{{ old('recipient_address') }}</textarea>
                                <small class="text-muted" style="font-size: 0.55rem;">One address line per line - will appear exactly as typed.</small>
                                @error('recipient_address')<div class="invalid-feedback" style="font-size: 0.6rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        @elseif($config['recipient_heading'])
                        <div class="mb-2">
                            <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.65rem;">Addressed as: {{ $config['recipient_heading'] }}</span>
                        </div>
                        @else
                        <div class="mb-2">
                            <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.65rem;">Addressed to the student's own record ({{ $student->formatted_name }})</span>
                        </div>
                        @endif

                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Subject Line</label>
                            <input type="text" name="subject" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" value="{{ old('subject', $defaultSubject) }}" placeholder="Leave blank for no RE: line">
                        </div>

                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Signatory Title</label>
                            <input type="text" name="signatory_title" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" value="{{ old('signatory_title', $config['signatory_title']) }}">
                        </div>

                        <x-numbered-letter-field
                            name="body_content"
                            label="Letter Content"
                            :rows="10"
                            :value="old('body_content', $defaultBody)"
                            :numberFrom="2"
                            :numbered="$config['numbered']"
                            required="true"
                            helpText="Pre-filled with the known facts about this graduate - edit freely, and add the specific purpose/remarks in further paragraphs."
                        />
                    </div>
                    <div class="card-footer py-2 px-3">
                        <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                            <i class="ti ti-file-certificate me-1"></i> Generate Letter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
