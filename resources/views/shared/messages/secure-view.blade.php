@extends('layouts.master')

@section('title', 'Secure Message Access')

@section('content')
<div class="container-fluid px-2">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Messages', 'url' => '#'],
        ['name' => 'Inbox', 'url' => '#']
    ]" />
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-2 px-3 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-lock me-1"></i>
                        <span class="fw-semibold">Secure Message Access</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(!isset($accessGranted))
                        <!-- Enter Code Form -->
                        <div class="text-center mb-4">
                            <i class="ti ti-key" style="font-size: 48px; color: #3B82F6;"></i>
                            <h5 class="mt-3">Enter One-Time Access Code</h5>
                            <p class="text-muted small">An access code was provided by the Vice Chancellor</p>
                        </div>

                        <form action="{{ route('messages.view-with-code', $message->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">One-Time Code</label>
                                <input type="text" name="one_time_code" class="form-control text-center" style="font-size: 1.2rem; letter-spacing: 2px;" placeholder="XXXX-XXXX-XXXX" required autocomplete="off">
                            </div>
                            <div class="alert alert-info small">
                                <i class="ti ti-info-circle me-1"></i>
                                This code is valid for 24 hours and can only be used once.
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary btn-sm px-4">
                                    <i class="ti ti-unlock me-1"></i> Access Message
                                </button>
                            </div>
                        </form>
                    @else
                        <!-- Message Content (Only shown after code is validated) -->
                        <div class="alert alert-success small mb-3">
                            <i class="ti ti-check-circle me-1"></i>
                            Access granted. This access is being logged for audit purposes.
                        </div>

                        <div class="border rounded p-3 mb-3">
                            <div class="border-bottom pb-2 mb-2">
                                <h5>{{ $message->subject }}</h5>
                                <div class="text-muted small">
                                    Message #: {{ $message->message_number }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <strong>From:</strong> {{ $message->sender->first_name ?? 'System' }} {{ $message->sender->last_name ?? '' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>To:</strong> {{ $message->recipient_names ?? $message->receiver->first_name ?? 'Unknown' }}
                                </div>
                            </div>
                            <div class="mb-2">
                                <strong>Sent:</strong> {{ $message->created_at->format('d M Y, H:i:s') }}
                            </div>
                            <div class="mt-3 pt-2 border-top">
                                <div style="line-height: 1.6;">
                                    {!! nl2br(e($message->message)) !!}
                                </div>
                            </div>
                            @if($message->attachments && $message->attachments->count() > 0)
                                <div class="mt-3 pt-2 border-top">
                                    <strong>Attachments:</strong>
                                    <ul>
                                        @foreach($message->attachments as $attachment)
                                            <li><a href="{{ Storage::url($attachment->file_path) }}" target="_blank">{{ $attachment->file_name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="alert alert-warning small">
                            <i class="ti ti-alert-triangle me-1"></i>
                            This access has been logged. Any further access will require a new request and approval.
                        </div>

                        <div class="text-center">
                            <a href="{{ route('messages.admin-list') }}" class="btn btn-secondary btn-sm">
                                <i class="ti ti-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Audit Notice -->
            <div class="text-center text-muted small mt-3">
                <i class="ti ti-shield"></i> All access to this message is logged for compliance purposes
            </div>
        </div>
    </div>
</div>
@endsection