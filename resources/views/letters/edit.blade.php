@extends('layouts.master')

@section('title', 'Edit Letter')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Letters', 'url' => route('letters.index')],
        ['name' => 'Edit Letter', 'url' => '#']
    ]" />
    
    <x-page-header title="EDIT LETTER" :description="'Editing: ' . $letter->title">
        <a href="{{ route('letters.show', $letter->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <div class="row g-2">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-text text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Letter Content</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('letters.update', $letter->id) }}" method="POST" id="letterForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Letter Title -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Subject/Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-sm rounded-sm" value="{{ old('title', $letter->title) }}" style="font-size: 0.7rem;" required placeholder="e.g., Application for Senior Lecturer Position">
                        </div>
                        
                        <!-- Recipient -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Recipient <span class="text-danger">*</span></label>
                            <select name="recipient_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                <option value="">Select Recipient</option>
                                @foreach($recipients as $recipient)
                                <option value="{{ $recipient->id }}" {{ old('recipient_id', $letter->recipient_id) == $recipient->id ? 'selected' : '' }}>
                                    {{ $recipient->full_name }} ({{ $recipient->staff_id ?? 'N/A' }}) - {{ $recipient->department->name ?? 'N/A' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Reference Format -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Reference Format (Optional)</label>
                            <select name="reference_format_id" id="reference_format_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="">Select Format (Auto-generate reference number)</option>
                                @foreach($referenceFormats as $format)
                                <option value="{{ $format->id }}" {{ old('reference_format_id', $letter->our_ref_format_id) == $format->id ? 'selected' : '' }}>
                                    {{ $format->name }} - {{ $format->format_pattern }}
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.55rem;">
                                @if($letter->letter_number)
                                    Current reference: <strong>{{ $letter->letter_number }}</strong> (already generated)
                                @else
                                    If selected, a reference number will be auto-generated when you submit.
                                @endif
                            </small>
                        </div>
                        
                        <!-- Your Reference (for responding to letters) -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Your Reference (Optional)</label>
                            <input type="text" name="your_ref" class="form-control form-control-sm rounded-sm" value="{{ old('your_ref', $letter->your_ref) }}" style="font-size: 0.7rem;" placeholder="e.g., Your letter dated 25 May 2026">
                            <small class="text-muted" style="font-size: 0.55rem;">Use this when responding to a letter from someone else.</small>
                        </div>
                        
                        <!-- Letter Type -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Letter Type</label>
                            <select name="type" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="general" {{ $letter->type == 'general' ? 'selected' : '' }}>General Correspondence</option>
                                <option value="application" {{ $letter->type == 'application' ? 'selected' : '' }}>Job Application</option>
                                <option value="request" {{ $letter->type == 'request' ? 'selected' : '' }}>Request Letter</option>
                                <option value="approval" {{ $letter->type == 'approval' ? 'selected' : '' }}>Approval Letter</option>
                                <option value="notification" {{ $letter->type == 'notification' ? 'selected' : '' }}>Notification</option>
                                <option value="circular" {{ $letter->type == 'circular' ? 'selected' : '' }}>Circular/Memo</option>
                            </select>
                        </div>
                        
                        <!-- Letter Content -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Letter Content <span class="text-danger">*</span></label>
                            <textarea name="content" id="letterContent" rows="14" class="form-control form-control-sm rounded-sm" style="font-size: 0.75rem; font-family: 'Times New Roman', serif; line-height: 1.5;" required placeholder="Write your letter here...&#10;&#10;Separate paragraphs with a blank line.&#10;&#10;The first paragraph will not be numbered.&#10;&#10;Subsequent paragraphs will be automatically numbered (2., 3., 4., etc.) if you enable auto-numbering below.">{{ old('content', $letter->raw_content ?? $letter->content) }}</textarea>
                        </div>
                        
                        <!-- Auto-numbering Option -->
                        <div class="mb-2 p-2 bg-light rounded-sm">
                            <div class="form-check form-switch">
                                <input type="hidden" name="auto_number_paragraphs" value="0">
                                <input type="checkbox" class="form-check-input" name="auto_number_paragraphs" value="1" id="autoNumberParagraphs" {{ $letter->auto_number_paragraphs ? 'checked' : '' }}>
                                <label class="form-check-label" for="autoNumberParagraphs" style="font-size: 0.65rem;">
                                    <i class="ti ti-list-numbers me-1"></i> Auto-number paragraphs (first paragraph no number, then 2., 3., 4., etc.)
                                </label>
                            </div>
                            <small class="text-muted" style="font-size: 0.55rem;">Separate paragraphs with a blank line. The system will automatically add numbers to paragraphs after the first one.</small>
                        </div>
                        
                        <!-- Preview Button -->
                        <div class="mb-2">
                            <button type="button" class="btn btn-outline-info btn-sm rounded-sm" onclick="showPreview()" style="font-size: 0.7rem;">
                                <i class="ti ti-eye me-1"></i> Preview Formatted Letter
                            </button>
                        </div>
                        
                        <!-- CC Recipients -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">CC (Carbon Copy) - Optional</label>
                            <select name="cc_recipients[]" class="form-select form-select-sm rounded-sm" multiple style="font-size: 0.7rem;" size="3">
                                @php
                                    $currentCC = json_decode($letter->cc_recipients, true) ?? [];
                                @endphp
                                <option value="">Select recipients to CC</option>
                                @foreach($recipients as $recipient)
                                <option value="{{ $recipient->id }}" {{ in_array($recipient->id, $currentCC) ? 'selected' : '' }}>
                                    {{ $recipient->full_name }} ({{ $recipient->staff_id ?? 'N/A' }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.55rem;">Hold Ctrl (Windows) or Cmd (Mac) to select multiple recipients.</small>
                        </div>
                        
                        <div class="d-flex gap-1 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-device-floppy me-1"></i> Save Changes
                            </button>
                            <button type="submit" name="action" value="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem;" onclick="return confirmSubmit()">
                                <i class="ti ti-send me-1"></i> Save & Submit for Approval
                            </button>
                            <a href="{{ route('letters.show', $letter->id) }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Info Panel -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-info-circle me-1"></i>
                    <span style="font-weight: 600;">Writing Guide</span>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="alert alert-light border mb-2 py-1 px-2 rounded-sm">
                        <strong class="d-block mb-1" style="font-size: 0.7rem;">📝 Paragraph Formatting</strong>
                        <small class="text-muted" style="font-size: 0.6rem;">
                            • Separate paragraphs with a <strong>blank line</strong><br>
                            • First paragraph: no number<br>
                            • Following paragraphs: auto-numbered (2., 3., 4.)<br>
                            • You can disable auto-numbering with the switch above
                        </small>
                    </div>
                    
                    <div class="alert alert-light border mb-2 py-1 px-2 rounded-sm">
                        <strong class="d-block mb-1" style="font-size: 0.7rem;">📎 Letter Components</strong>
                        <small class="text-muted" style="font-size: 0.6rem;">
                            • University letterhead<br>
                            • Reference number (if format selected)<br>
                            • Date<br>
                            • Secure code (for verification)<br>
                            • Recipient address<br>
                            • Subject line<br>
                            • Your content<br>
                            • Signature block<br>
                            • Footer with verification
                        </small>
                    </div>
                    
                    <div class="alert alert-light border mb-0 py-1 px-2 rounded-sm">
                        <strong class="d-block mb-1" style="font-size: 0.7rem;">📌 Letter Workflow</strong>
                        <small class="text-muted" style="font-size: 0.6rem;">
                            1. Draft → 2. Submit for Approval → 3. HOD Approval → 4. Dean Approval → 5. DVC Approval → 6. VC Signature → 7. Print
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Current Letter Info -->
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-file-info me-1"></i>
                    <span style="font-weight: 600;">Current Letter Info</span>
                </div>
                <div class="card-body py-2 px-2">
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.7rem;">
                        <tr><td class="text-muted ps-0" style="width: 40%;">Created By:</td><td>{{ $letter->sender->full_name ?? 'N/A' }}</td></tr>
                        <tr><td class="text-muted ps-0">Created At:</td><td>{{ $letter->created_at->format('d M Y, H:i') }}</td></tr>
                        <tr><td class="text-muted ps-0">Last Modified:</td><td>{{ $letter->updated_at->format('d M Y, H:i') }}</td></tr>
                        @if($letter->letter_number)
                        <tr><td class="text-muted ps-0">Reference:</td><td class="fw-semibold">{{ $letter->letter_number }}</td></tr>
                        @endif
                        <tr class="border-top mt-1">
                            <td colspan="2" class="ps-0 pt-1">
                                <div class="text-muted" style="font-size: 0.55rem;">
                                    <i class="ti ti-info-circle"></i> 
                                    @if($letter->letter_number)
                                        Reference number already assigned. Submitting will send for approval.
                                    @else
                                        Select a reference format above to auto-generate reference number on submission.
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Preview Section -->
            <div class="card rounded-sm mt-2" id="previewCard" style="display: none;">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <i class="ti ti-eye me-1"></i>
                        <span style="font-weight: 600;">Formatted Preview</span>
                        <button type="button" class="btn-close py-0" style="font-size: 0.5rem;" onclick="hidePreview()"></button>
                    </div>
                </div>
                <div class="card-body p-2" id="previewContent" style="max-height: 400px; overflow-y: auto; font-family: 'Times New Roman', serif; font-size: 0.7rem; background: #fff;">
                    <div class="text-muted text-center py-3">Click preview to see formatted letter</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function formatParagraphs(text, autoNumber) {
        if (!autoNumber) return text;
        
        let paragraphs = text.split(/\n\s*\n/);
        let formatted = [];
        
        for (let i = 0; i < paragraphs.length; i++) {
            let para = paragraphs[i].trim();
            if (para === "") continue;
            
            if (i === 0) {
                formatted.push(para);
            } else {
                formatted.push((i + 1) + ". " + para);
            }
        }
        return formatted.join("\n\n");
    }
    
    function showPreview() {
        const content = document.getElementById('letterContent').value;
        const autoNumber = document.getElementById('autoNumberParagraphs').checked;
        const formatted = formatParagraphs(content, autoNumber);
        
        const previewDiv = document.getElementById('previewContent');
        const previewCard = document.getElementById('previewCard');
        
        let html = formatted.replace(/\n\n/g, '</p><p style="margin-bottom: 0.5rem;">');
        html = '<p style="margin-bottom: 0.5rem;">' + html + '</p>';
        html = html.replace(/\n/g, '<br>');
        
        previewDiv.innerHTML = html || '<div class="text-muted text-center py-3">No content to preview</div>';
        previewCard.style.display = 'block';
        previewCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    function hidePreview() {
        document.getElementById('previewCard').style.display = 'none';
    }
    
    function confirmSubmit() {
        const hasReference = {{ $letter->letter_number ? 'true' : 'false' }};
        const formatSelected = document.getElementById('reference_format_id').value;
        
        if (!hasReference && !formatSelected) {
            return confirm('No reference format selected. The letter will be submitted without a reference number.\n\nContinue?');
        }
        return confirm('Submit this letter for approval?\n\nOnce submitted, you cannot edit it further.');
    }
    
    // Auto-save draft indicator
    let typingTimer;
    document.getElementById('letterContent').addEventListener('keyup', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function() {
            // Auto-save logic can be added here
            console.log('Auto-save would trigger here');
        }, 30000);
    });
</script>
@endsection