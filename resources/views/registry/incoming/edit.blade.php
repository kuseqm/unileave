@extends('layouts.app')

@section('title', 'Edit Incoming Letter')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Edit Incoming Letter: {{ $letter->ref_number }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('registry.incoming.update', $letter->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Sender</label>
                            <input type="text" name="sender" class="form-control @error('sender') is-invalid @enderror" value="{{ old('sender', $letter->sender) }}" required>
                            @error('sender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Date Received</label>
                            <input type="date" name="date_received" class="form-control @error('date_received') is-invalid @enderror" value="{{ old('date_received', $letter->date_received->format('Y-m-d')) }}" required>
                            @error('date_received') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Addressee Office</label>
                            <select name="office_id" class="form-select @error('office_id') is-invalid @enderror" required>
                                <option value="">Select Office</option>
                                @foreach($offices as $office)
                                <option value="{{ $office->id }}" {{ old('office_id', $letter->office_id) == $office->id ? 'selected' : '' }}>
                                    {{ $office->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('office_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="normal" {{ $letter->priority == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="urgent" {{ $letter->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                <option value="confidential" {{ $letter->priority == 'confidential' ? 'selected' : '' }}>Confidential</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label required">Subject</label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject', $letter->subject) }}" required>
                            @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="received" {{ $letter->status == 'received' ? 'selected' : '' }}>Received</option>
                                <option value="assigned" {{ $letter->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="in_progress" {{ $letter->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="replied" {{ $letter->status == 'replied' ? 'selected' : '' }}>Replied</option>
                                <option value="closed" {{ $letter->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3">{{ old('remarks', $letter->remarks) }}</textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('registry.incoming.show', $letter->id) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Letter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection