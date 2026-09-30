@extends('layouts.master')

@section('title', 'Secure Message View')

@section('content')
<div class="container-fluid px-2">
    <!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['name' => 'Messages', 'url' => route('messages.inbox')],
    ['name' => 'Message Directory', 'url' => route('messages.admin-list')],
    ['name' => 'Secure View', 'url' => '#']
    ]" />
    <x-page-header title="SECURE MESSAGE VIEW" description="Authorized access - Viewing encrypted message content">
        <a href="{{ route('messages.metadata', $message->id) }}" class="btn btn-outline-secondary btn-sm rounded-sm">
            <i class="ti ti-arrow-left me-1"></i> Back to Metadata
        </a>
    </x-page-header>

    <div class="row">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-2 px-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="ti ti-mail me-1"></i>
                            <span class="fw-semibold">{{ $message->subject }}</span>
                        </div>
                        <div>
                            <span class="badge bg-success rounded-pill">Authorized Access</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Access Notice -->
                    <x-alert-info>
    <strong>Authorized Access</strong> - You are viewing this message with special authorization.
                        @if(isset($accessRequest) && $accessRequest)
                            <br>Access granted via one-time code on {{ $accessRequest->viewed_at ? $accessRequest->viewed_at->format('d M Y, H:i:s') : 'recently' }}.
                        @endif
</x-alert-info>

                    <!-- Message Header -->
                    <div class="border-bottom pb-2 mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-muted small">From:</div>
                                <div class="fw-semibold">{{ $message->sender->first_name ?? 'System' }} {{ $message->sender->last_name ?? '' }}</div>
                                <div class="text-muted small">{{ $message->sender->email ?? '' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">To:</div>
                                <div class="fw-semibold">{{ $message->recipient_names ?? $message->receiver->first_name ?? 'Unknown' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Date Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="text-muted small">
                                <i class="ti ti-calendar me-1"></i> Sent: {{ $message->created_at->format('d M Y, H:i:s') }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($message->read_at)
                                <div class="text-muted small">
                                    <i class="ti ti-eye me-1"></i> Read: {{ $message->read_at->format('d M Y, H:i:s') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Message Body -->
                    <div class="mb-4">
                        <strong>Message:</strong>
                        <div class="mt-2 p-3 bg-light rounded" style="line-height: 1.6;">
                            {!! nl2br(e($message->message)) !!}
                        </div>
                    </div>

                    <!-- Attachments -->
                    @if($message->attachments && $message->attachments->count() > 0)
                        <div class="mb-3">
                            <strong><i class="ti ti-paperclip me-1"></i> Attachments:</strong>
                            <ul class="mt-1">
                                @foreach($message->attachments as $attachment)
                                    <li>
                                        <a href="{{ route('messages.attachments.download', $attachment->id) }}" target="_blank">
                                            {{ $attachment->file_name }}
                                        </a>
                                        <span class="text-muted">({{ number_format($attachment->file_size / 1024, 2) }} KB)</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Security Notice -->
                    <x-alert-warning>
    <strong>Security Notice:</strong> This access has been logged for audit purposes. Any unauthorized sharing of this content is prohibited.
</x-alert-warning>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Context Panel (30%) -->
        <div class="col-lg-4">
            <div class="card rounded-sm border-0 shadow-sm">
                <div class="card-header py-2 px-3 bg-light">
                    <i class="ti ti-info-circle me-1"></i>
                    <span class="fw-semibold">Access Information</span>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <i class="ti ti-key text-success me-1"></i>
                        <strong class="small">Access Method</strong>
                        <p class="text-muted small mt-1 mb-0">You are viewing this message with authorized access granted by the Vice Chancellor.</p>
                    </div>
                    
                    <div class="mb-3">
                        <i class="ti ti-history text-info me-1"></i>
                        <strong class="small">Audit Trail</strong>
                        <p class="text-muted small mt-1 mb-0">This access has been recorded in the audit log for compliance purposes.</p>
                    </div>
                    
                    <x-alert-secondary>
    <strong>Need to request access?</strong>
                        <p class="mb-0 mt-1">HR officers can request access via the <a href="{{ route('messages.metadata', $message->id) }}">Metadata page</a>.</p>
</x-alert-secondary>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection