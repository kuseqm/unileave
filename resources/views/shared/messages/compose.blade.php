@extends('layouts.master')

@section('title', $forwardMessage ? 'Forward Message' : ($replyTo ? 'Reply to Message' : 'Compose Message'))

@section('content')
<div class="container-fluid px-2">
    <!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['name' => 'Messages', 'url' => route('messages.inbox')],
    ['name' => 'Compose', 'url' => '#']
    ]" />

    <x-page-header :title="$forwardMessage ? 'FORWARD MESSAGE' : ($replyTo ? 'REPLY TO MESSAGE' : 'COMPOSE MESSAGE')" 
                   :description="$forwardMessage ? 'Forward this message to new recipients' : ($replyTo ? 'Send a reply to the message' : 'Send a new message')">
        <a href="{{ route('messages.inbox') }}" class="btn btn-outline-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back to Inbox
        </a>
    </x-page-header>

    <!-- ERROR DISPLAY SECTION -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Please fix the following:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-body p-3">
                    <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data" id="messageForm">
                        @csrf
                        
                        <!-- To: Field with AJAX Search -->
                        <div class="mb-2">
                            <label class="form-label fw-semibold">
                                To: 
                                <span class="text-muted" id="recipientCount"></span>
                            </label>
                            
                            <!-- Hidden input to ensure empty array is sent -->
                            <input type="hidden" name="recipient_ids" value="">
                            
                            <select name="recipient_ids[]" id="userSelect" class="form-select @error('recipient_ids') is-invalid @enderror" multiple="multiple" style="width: 100%;">
                                @php
                                    // Get pre-selected recipients for Reply All or Reply
                                    $preselectedIds = [];
                                    if(!empty($replyAllRecipients)) {
                                        $preselectedIds = $replyAllRecipients;
                                    } elseif($selectedUser) {
                                        $preselectedIds = is_array($selectedUser) ? $selectedUser : [$selectedUser];
                                    } elseif(old('recipient_ids')) {
                                        $preselectedIds = is_array(old('recipient_ids')) ? old('recipient_ids') : [old('recipient_ids')];
                                    }
                                @endphp
                                
                                @if(!empty($preselectedIds))
                                    @php
                                        $preselectedUsers = \App\Models\User::whereIn('id', $preselectedIds)->get();
                                    @endphp
                                    @foreach($preselectedUsers as $user)
                                        <option value="{{ $user->id }}" selected>
                                            {{ $user->first_name }} {{ $user->last_name }} ({{ $user->staff_id ?? $user->email }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted">Type at least 2 characters to search. Select multiple recipients (max 20).</small>
                            @error('recipient_ids') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Info Alerts for Reply/Reply All/Forward -->
                        @if(isset($replyTo) && $replyTo)
                            <div class="alert alert-info py-1 px-2 small">
                                <i class="ti ti-corner-up-right me-1"></i> 
                                <strong>Replying to:</strong> {{ $replyTo->sender->first_name }} {{ $replyTo->sender->last_name }}
                                @if(!empty($replyAllRecipients))
                                    <br><small>Reply All mode: Also sending to other recipients on this conversation</small>
                                @endif
                            </div>
                        @endif

                        @if(isset($forwardMessage) && $forwardMessage)
                            <div class="alert alert-secondary py-1 px-2 small">
                                <i class="ti ti-corner-up-left me-1"></i> 
                                <strong>Forwarding message from:</strong> {{ $forwardMessage->sender->first_name }} {{ $forwardMessage->sender->last_name }}
                                <br><small>Original sent: {{ $forwardMessage->created_at ? $forwardMessage->created_at->format('d M Y, H:i') : 'Unknown' }}</small>
                            </div>
                        @endif

                        <!-- Subject -->
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Subject:</label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" 
                                   value="{{ old('subject', isset($subject) ? $subject : (isset($replyTo) ? 'Re: ' . $replyTo->subject : '')) }}" required>
                            @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Priority, Type, Deadline -->
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Priority:</label>
                                <select name="priority" class="form-select">
                                    <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Type:</label>
                                <select name="message_type" class="form-select">
                                    <option value="general" {{ old('message_type') == 'general' ? 'selected' : '' }}>General</option>
                                    <option value="approval" {{ old('message_type') == 'approval' ? 'selected' : '' }}>Approval Request</option>
                                    <option value="task" {{ old('message_type') == 'task' ? 'selected' : '' }}>Task Assignment</option>
                                    <option value="minute" {{ old('message_type') == 'minute' ? 'selected' : '' }}>Minute</option>
                                    <option value="notification" {{ old('message_type') == 'notification' ? 'selected' : '' }}>Notification</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Deadline:</label>
                                <input type="date" name="deadline" class="form-control" value="{{ old('deadline') }}">
                            </div>
                        </div>

                        <!-- Message Body -->
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Message:</label>
                            <textarea name="message" rows="8" class="form-control @error('message') is-invalid @enderror" required>{{ old('message', isset($messageContent) ? $messageContent : '') }}</textarea>
                            @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Attachments -->
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Attachments:</label>
                            <input type="file" name="attachments[]" class="form-control" multiple>
                            <small class="text-muted">Max 10MB per file. Allowed: PDF, DOC, DOCX, JPG, PNG</small>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-send me-1"></i> 
                                {{ isset($forwardMessage) ? 'Forward Message' : (isset($replyTo) ? 'Send Reply' : 'Send Message') }}
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.href='{{ route('messages.inbox') }}'">
                                <i class="ti ti-x me-1"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-tips me-1"></i> Tips
                </div>
                <div class="card-body p-2 small">
                    <ul class="mb-0 ps-3">
                        <li><strong>Search:</strong> Type name, email, or staff ID (min 2 characters)</li>
                        <li><strong>Select:</strong> Click on search results to add recipients</li>
                        <li><strong>Remove:</strong> Click the 'x' on a recipient tag to remove</li>
                        <li><strong>Reply:</strong> Sends only to original sender</li>
                        <li><strong>Reply All:</strong> Sends to all original recipients</li>
                        <li><strong>Forward:</strong> Send to new recipients with original message quoted</li>
                        <li>Use high/urgent priority for time-sensitive matters</li>
                        <li>Set deadlines to track response times</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 with AJAX search
    $('#userSelect').select2({
        placeholder: "Search by name, email, or staff ID...",
        allowClear: true,
        width: '100%',
        minimumInputLength: 2,
        multiple: true,
        maximumSelectionLength: 20,
        ajax: {
            url: '{{ route("messages.users.search") }}',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.results
                };
            },
            cache: true
        },
        templateResult: formatUser,
        templateSelection: formatUserSelection
    });
    
    // Format how each user appears in the dropdown
    function formatUser(user) {
        if (user.loading) {
            return user.text;
        }
        
        var $container = $(
            '<div><strong>' + user.text.split(' - ')[0] + '</strong><br>' +
            '<small class="text-muted">' + (user.text.split(' - ')[1] || '') + '</small></div>'
        );
        return $container;
    }
    
    // Format how selected user appears in the tags
    function formatUserSelection(user) {
        if (user.text) {
            return user.text.split(' - ')[0];
        }
        return user.id;
    }
    
    // Update recipient count
    function updateRecipientCount() {
        var count = $('#userSelect').val() ? $('#userSelect').val().length : 0;
        var maxRecipients = 20;
        $('#recipientCount').text(count > 0 ? '(' + count + '/20 selected)' : '');
        
        if (count >= maxRecipients) {
            $('#recipientCount').addClass('text-warning');
        } else {
            $('#recipientCount').removeClass('text-warning');
        }
    }
    
    $('#userSelect').on('select2:select select2:unselect', updateRecipientCount);
    updateRecipientCount();
    
    // Validate before submit
    $('#messageForm').on('submit', function(e) {
        var selected = $('#userSelect').val();
        console.log('Selected recipient IDs:', selected);
        if (!selected || selected.length === 0) {
            e.preventDefault();
            alert('Please select at least one recipient.');
            return false;
        }
        return true;
    });
});
</script>

<style>
/* Select2 custom styling */
.select2-container--default .select2-selection--multiple {
    border-color: #e8e2d5;
    border-radius: 0.375rem;
    min-height: 80px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #eecbc4;
    border: none;
    border-radius: 0.25rem;
    color: #1e293b;
    font-size: 0.8rem;
    padding: 4px 8px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #1e293b;
    margin-right: 6px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    background-color: transparent;
    color: #dc2626;
}

.select2-dropdown {
    border-color: #e8e2d5;
    border-radius: 0.375rem;
}

.select2-search__field {
    border-color: #e8e2d5 !important;
    border-radius: 0.375rem !important;
}
</style>
@endsection