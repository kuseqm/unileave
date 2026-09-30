@extends('layouts.master')

@section('title', 'Message Details')

@section('content')
<div class="container-fluid px-2">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Messages', 'url' => '#'],
        ['name' => 'Inbox', 'url' => '#']
    ]" />
    
    <x-page-header title="MESSAGE DETAILS" description="View and respond to message">
        <a href="{{ route('messages.inbox') }}" class="btn btn-outline-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back to Inbox
        </a>
    </x-page-header>

    <div class="row">
        <div class="col-lg-8">
            <!-- Message Card -->
            <div class="card rounded-sm mb-3">
                <div class="card-header py-3 px-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $message->subject }}</h5>
                            <div class="text-muted small">
                                <i class="ti ti-mail me-1"></i> {{ $message->message_number }}
                            </div>
                        </div>
                        <div>
                            <span class="badge bg-{{ $message->priority == 'urgent' ? 'danger' : ($message->priority == 'high' ? 'warning' : 'secondary') }} rounded-pill">
                                {{ ucfirst($message->priority) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Sender/Receiver Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-sm rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="ti ti-user text-primary" style="font-size: 20px;"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">FROM</div>
                                    <div class="fw-semibold">{{ $message->sender->first_name ?? '' }} {{ $message->sender->last_name ?? '' }}</div>
                                    <div class="text-muted small">{{ $message->sender->email ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-sm rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="ti ti-users text-success" style="font-size: 20px;"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">TO</div>
                                    <div class="fw-semibold">{{ $message->recipient_names ?? $message->receiver->first_name ?? 'Unknown' }}</div>
                                    <div class="text-muted small">{{ $message->receiver->email ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Date Info -->
                    <div class="d-flex gap-3 mb-4 pb-2 border-bottom">
                        <div class="text-muted small">
                            <i class="ti ti-calendar me-1"></i> Sent: {{ $message->created_at->format('d M Y, H:i') }}
                        </div>
                        @if($message->read_at)
                            <div class="text-muted small">
                                <i class="ti ti-eye me-1"></i> Read: {{ $message->read_at->format('d M Y, H:i') }}
                            </div>
                        @endif
                        @if($message->deadline)
                            <div class="text-muted small">
                                <i class="ti ti-alarm-clock me-1"></i> Deadline: {{ $message->deadline->format('d M Y') }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Message Body -->
                    <div class="mb-4">
                        <div class="p-3 bg-light rounded" style="line-height: 1.6;">
                            {!! nl2br(e($message->message)) !!}
                        </div>
                    </div>
                    
                    <!-- Attachments -->
                    @if($message->attachments && $message->attachments->count() > 0)
                        <div class="mb-4">
                            <div class="fw-semibold mb-2">Attachments</div>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach($message->attachments as $attachment)
                                    <a href="{{ route('messages.attachments.download', $attachment->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill">
                                        <i class="ti ti-paperclip me-1"></i> {{ $attachment->file_name }}
                                        <span class="text-muted ms-1">({{ number_format($attachment->file_size / 1024, 1) }} KB)</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Action Buttons -->
                <div class="card-footer bg-light py-3 px-4">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('messages.create', ['reply_to' => $message->id]) }}" class="btn btn-primary btn-sm rounded-sm">
                            <i class="ti ti-corner-up-right me-1"></i> Reply
                        </a>
                        <a href="{{ route('messages.create', ['reply_all' => $message->id]) }}" class="btn btn-info btn-sm rounded-sm">
                            <i class="ti ti-users me-1"></i> Reply All
                        </a>
                        <a href="{{ route('messages.create', ['forward' => $message->id]) }}" class="btn btn-secondary btn-sm rounded-sm">
                            <i class="ti ti-corner-up-left me-1"></i> Forward
                        </a>
                        <button onclick="archiveMessage({{ $message->id }})" class="btn btn-outline-secondary btn-sm rounded-sm">
                            <i class="ti ti-archive me-1"></i> Archive
                        </button>
                        <button onclick="toggleStar({{ $message->id }})" class="btn btn-outline-warning btn-sm rounded-sm">
                            <i class="ti ti-star {{ $message->is_starred ? 'text-warning' : '' }}"></i>
                            {{ $message->is_starred ? 'Unstar' : 'Star' }}
                        </button>
                        
                        <!-- Delete Button - Using YOUR modal pattern (like staff management) -->
                        <button type="button" 
                                onclick="showDeleteModal({{ $message->id }}, '{{ addslashes($message->subject) }}', null, '{{ route('messages.destroy', $message->id) }}')"
                                class="btn btn-outline-danger btn-sm rounded-sm ms-auto">
                            <i class="ti ti-trash me-1"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Action Timeline -->
            <div class="card rounded-sm mb-3">
                <div class="card-header py-2 bg-light">
                    <i class="ti ti-activity me-1"></i> Activity Timeline
                </div>
                <div class="card-body p-3">
                    @if($message->actions && $message->actions->count() > 0)
                        @foreach($message->actions as $action)
                            <div class="d-flex gap-2 mb-3">
                                <div class="timeline-icon">
                                    <i class="ti ti-check-circle text-success"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small">{{ ucfirst($action->action) }}</div>
                                    <div class="text-muted small">{{ $action->user->first_name ?? 'System' }} • {{ $action->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="ti ti-clock"></i>
                            <p class="small mb-0">No activity recorded yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function archiveMessage(id) {
    fetch(`/messages/${id}/archive`, { 
        method: 'POST', 
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } 
    })
    .then(() => window.location.reload())
    .catch(error => console.error('Error:', error));
}

function toggleStar(id) {
    fetch(`/messages/${id}/star`, { 
        method: 'POST', 
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } 
    })
    .then(() => window.location.reload())
    .catch(error => console.error('Error:', error));
}
</script>

<style>
.avatar-sm {
    width: 45px;
    height: 45px;
}
.timeline-icon {
    width: 24px;
}
</style>
@endsection