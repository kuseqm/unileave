@extends('layouts.master')

@section('title', 'Write New Letter')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Letters', 'url' => route('letters.index')],
        ['name' => 'Write Letter', 'url' => '#']
    ]" />
    
    <x-page-header title="WRITE NEW LETTER" description="Create a new official letter">
        <a href="{{ route('letters.index') }}" class="btn btn-outline-secondary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
            <i class="ti ti-arrow-left me-1"></i> Back
        </a>
    </x-page-header>
    
    @if($errors->any())
    <x-alert-danger :session="false">
    <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
</x-alert-danger>
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
                    <form action="{{ route('letters.store') }}" method="POST" id="letterForm">
                        @csrf
                        
                        <!-- Letter Title -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Subject/Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-sm rounded-sm" value="{{ old('title') }}" style="font-size: 0.7rem;" required placeholder="e.g., Application for Senior Lecturer Position">
                        </div>
                        
                        <!-- Recipient Type Selection -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Recipient Type</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input type="radio" name="recipient_type" id="recipient_staff" value="staff" class="form-check-input" checked>
                                    <label class="form-check-label" for="recipient_staff" style="font-size: 0.65rem;">Staff Member</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="recipient_type" id="recipient_custom" value="custom" class="form-check-input">
                                    <label class="form-check-label" for="recipient_custom" style="font-size: 0.65rem;">External / Other</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="recipient_type" id="recipient_multiple" value="multiple" class="form-check-input">
                                    <label class="form-check-label" for="recipient_multiple" style="font-size: 0.65rem;">Multiple Recipients</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Staff Selection (shown when staff selected) -->
                        <div id="staff_recipient_block" class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Select Recipient <span class="text-danger">*</span></label>
                            <select name="recipient_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="">Select Recipient</option>
                                @foreach($recipients as $recipient)
                                <option value="{{ $recipient->id }}" {{ old('recipient_id') == $recipient->id ? 'selected' : '' }}>
                                    {{ $recipient->full_name }} ({{ $recipient->staff_id ?? 'N/A' }}) - {{ $recipient->department->name ?? 'N/A' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Custom Recipient Address (shown when custom selected) -->
                        <div id="custom_recipient_block" class="mb-2" style="display: none;">
                            <label class="form-label" style="font-size: 0.65rem;">Recipient Address <span class="text-danger">*</span></label>
                            <textarea name="recipient_address" rows="4" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;" placeholder="Enter recipient name and address...&#10;&#10;Example:&#10;Ministry of Education&#10;P.O. Box 12345&#10;Dodoma, Tanzania">{{ old('recipient_address') }}</textarea>
                            <small class="text-muted" style="font-size: 0.55rem;">Enter full name and address of the recipient.</small>
                        </div>
                        
                        <!-- Multiple Recipients Block (shown when multiple selected) -->
                        <div id="multiple_recipient_block" class="mb-2" style="display: none;">
                            <label class="form-label" style="font-size: 0.65rem;">Recipients <span class="text-danger">*</span></label>
                            <div id="recipientsList">
                                <div class="recipient-entry row g-1 mb-2">
                                    <div class="col-md-6">
                                        <input type="text" name="recipients[0][name]" class="form-control form-control-sm rounded-sm" placeholder="Full Name" style="font-size: 0.7rem;" value="{{ old('recipients.0.name') }}">
                                    </div>
                                    <div class="col-md-5">
                                        <textarea name="recipients[0][address]" rows="2" class="form-control form-control-sm rounded-sm" placeholder="Address (Optional)" style="font-size: 0.7rem;">{{ old('recipients.0.address') }}</textarea>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-recipient" style="font-size: 0.6rem; padding: 0.25rem 0.5rem;" disabled>
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="addRecipientBtn" class="btn btn-sm btn-outline-secondary mt-1" style="font-size: 0.65rem;">
                                <i class="ti ti-plus me-1"></i> Add Another Recipient
                            </button>
                            <small class="text-muted d-block mt-1" style="font-size: 0.55rem;">Add multiple recipients if this letter is addressed to multiple people.</small>
                        </div>
                        
                        <!-- Reference Format -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Reference Format (Optional)</label>
                            <select name="reference_format_id" id="reference_format_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="">Select Format (Auto-generate reference number)</option>
                                @foreach($referenceFormats as $format)
                                <option value="{{ $format->id }}" {{ old('reference_format_id') == $format->id ? 'selected' : '' }}>
                                    {{ $format->name }} - {{ $format->format_pattern }}
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.55rem;">If selected, a reference number will be auto-generated when you submit.</small>
                        </div>
                        
                        <!-- Your Reference (for responding to letters) -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Your Reference (Optional)</label>
                            <input type="text" name="your_ref" class="form-control form-control-sm rounded-sm" value="{{ old('your_ref') }}" style="font-size: 0.7rem;" placeholder="e.g., Your letter dated 25 May 2026">
                            <small class="text-muted" style="font-size: 0.55rem;">Use this when responding to a letter from someone else.</small>
                        </div>
                        
                        <!-- Letter Type -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Letter Type</label>
                            <select name="type" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="general">General Correspondence</option>
                                <option value="application">Job Application</option>
                                <option value="request">Request Letter</option>
                                <option value="approval">Approval Letter</option>
                                <option value="notification">Notification</option>
                                <option value="circular">Circular/Memo</option>
                            </select>
                        </div>
                        
                        <!-- Letter Content -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">Letter Content <span class="text-danger">*</span></label>
                            <textarea name="content" id="letterContent" rows="12" class="form-control form-control-sm rounded-sm" style="font-size: 0.75rem; font-family: 'Times New Roman', serif; line-height: 1.5;" required placeholder="Write your letter here...&#10;&#10;Separate paragraphs with a blank line.&#10;&#10;The first paragraph will not be numbered.&#10;&#10;Subsequent paragraphs will be automatically numbered (2., 3., 4., etc.) if you enable auto-numbering below.">{{ old('content') }}</textarea>
                        </div>
                        
                        <!-- Auto-numbering Option -->
                        <div class="mb-2 p-2 bg-light rounded-sm">
                            <div class="form-check form-switch">
                                <input type="hidden" name="auto_number_paragraphs" value="0">
                                <input type="checkbox" class="form-check-input" name="auto_number_paragraphs" value="1" id="autoNumberParagraphs" checked>
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
                                <option value="">Select recipients to CC</option>
                                @foreach($recipients as $recipient)
                                <option value="{{ $recipient->id }}">{{ $recipient->full_name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.55rem;">Hold Ctrl to select multiple recipients.</small>
                        </div>
                        
                        <div class="d-flex gap-1 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-device-floppy me-1"></i> Save as Draft
                            </button>
                            <button type="submit" name="action" value="submit" class="btn btn-success btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-send me-1"></i> Submit for Approval
                            </button>
                            <a href="{{ route('letters.index') }}" class="btn btn-secondary btn-sm rounded-sm" style="font-size: 0.7rem;">Cancel</a>
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
                    <x-alert-secondary>
    <strong class="d-block mb-1" style="font-size: 0.7rem;">📝 Paragraph Formatting</strong>
                        <small class="text-muted" style="font-size: 0.6rem;">
                            • Separate paragraphs with a <strong>blank line</strong><br>
                            • First paragraph: no number<br>
                            • Following paragraphs: auto-numbered (2., 3., 4.)<br>
                            • You can disable auto-numbering with the switch above
                        </small>
</x-alert-secondary>
                    
                    <x-alert-secondary>
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
</x-alert-secondary>
                    
                    <x-alert-secondary>
    <strong class="d-block mb-1" style="font-size: 0.7rem;">📌 Recipient Types</strong>
                        <small class="text-muted" style="font-size: 0.6rem;">
                            • <strong>Staff Member:</strong> Select from registered staff<br>
                            • <strong>External/Other:</strong> Enter custom address<br>
                            • <strong>Multiple Recipients:</strong> Add multiple recipients to the same letter
                        </small>
</x-alert-secondary>
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
    // Get elements
    const staffRadio = document.getElementById('recipient_staff');
    const customRadio = document.getElementById('recipient_custom');
    const multipleRadio = document.getElementById('recipient_multiple');
    const staffBlock = document.getElementById('staff_recipient_block');
    const customBlock = document.getElementById('custom_recipient_block');
    const multipleBlock = document.getElementById('multiple_recipient_block');
    const recipientIdSelect = document.querySelector('select[name="recipient_id"]');
    const recipientAddressTextarea = document.querySelector('textarea[name="recipient_address"]');
    const recipientsList = document.getElementById('recipientsList');
    let recipientCount = 1;
    
    // Toggle recipient blocks
    function toggleRecipientBlocks() {
        if (staffRadio.checked) {
            staffBlock.style.display = 'block';
            customBlock.style.display = 'none';
            multipleBlock.style.display = 'none';
            recipientIdSelect.required = true;
            if (recipientAddressTextarea) recipientAddressTextarea.required = false;
        } else if (customRadio.checked) {
            staffBlock.style.display = 'none';
            customBlock.style.display = 'block';
            multipleBlock.style.display = 'none';
            recipientIdSelect.required = false;
            if (recipientAddressTextarea) recipientAddressTextarea.required = true;
        } else if (multipleRadio.checked) {
            staffBlock.style.display = 'none';
            customBlock.style.display = 'none';
            multipleBlock.style.display = 'block';
            recipientIdSelect.required = false;
            if (recipientAddressTextarea) recipientAddressTextarea.required = false;
        }
    }
    
    staffRadio.addEventListener('change', toggleRecipientBlocks);
    customRadio.addEventListener('change', toggleRecipientBlocks);
    multipleRadio.addEventListener('change', toggleRecipientBlocks);
    
    // Add recipient row for multiple recipients
    document.getElementById('addRecipientBtn').addEventListener('click', function() {
        recipientCount++;
        const newRow = document.createElement('div');
        newRow.className = 'recipient-entry row g-1 mb-2';
        newRow.innerHTML = `
            <div class="col-md-6">
                <input type="text" name="recipients[${recipientCount}][name]" class="form-control form-control-sm rounded-sm" placeholder="Full Name" style="font-size: 0.7rem;">
            </div>
            <div class="col-md-5">
                <textarea name="recipients[${recipientCount}][address]" rows="2" class="form-control form-control-sm rounded-sm" placeholder="Address (Optional)" style="font-size: 0.7rem;"></textarea>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger remove-recipient" style="font-size: 0.6rem; padding: 0.25rem 0.5rem;">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        `;
        recipientsList.appendChild(newRow);
        
        // Enable/disable remove buttons
        updateRemoveButtons();
    });
    
    // Remove recipient row (delegation)
    recipientsList.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.remove-recipient');
        if (removeBtn) {
            const row = removeBtn.closest('.recipient-entry');
            if (row && recipientsList.children.length > 1) {
                row.remove();
                recipientCount--;
                updateRemoveButtons();
            }
        }
    });
    
    function updateRemoveButtons() {
        const rows = recipientsList.querySelectorAll('.recipient-entry');
        const removeBtns = recipientsList.querySelectorAll('.remove-recipient');
        
        removeBtns.forEach((btn, idx) => {
            if (idx === 0 && rows.length === 1) {
                btn.disabled = true;
            } else {
                btn.disabled = false;
            }
        });
    }
    
    // Format paragraphs for preview
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
    
    // Auto-save draft indicator
    let typingTimer;
    document.getElementById('letterContent').addEventListener('keyup', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function() {
            console.log('Auto-save triggered');
        }, 30000);
    });
    
    // Initialize on page load
    toggleRecipientBlocks();
    updateRemoveButtons();
</script>
@endsection