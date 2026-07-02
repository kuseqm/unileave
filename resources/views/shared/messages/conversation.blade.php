@extends('layouts.master')

@section('title', 'Conversation')

@section('content')
<div class="container-fluid px-2">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Messages', 'url' => '#'],
        ['name' => 'Inbox', 'url' => '#']
    ]" />
    
    <x-page-header title="CONVERSATION" description="Chat with {{ $otherUser->first_name ?? '' }} {{ $otherUser->last_name ?? '' }}">
        <a href="{{ route('messages.inbox') }}" class="btn btn-outline-secondary btn-sm"><i class="ti ti-arrow-left me-1"></i> Back</a>
    </x-page-header>

    <div class="card rounded-sm">
        <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">
            @forelse($messages ?? [] as $message)
                <div class="mb-3 {{ $message->sender_id == auth()->id() ? 'text-end' : '' }}">
                    <div class="d-inline-block p-2 rounded {{ $message->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 70%;">
                        <div class="small">{{ $message->message }}</div>
                        <div class="small mt-1 opacity-75">{{ $message->created_at->format('H:i') }}</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="ti ti-message" style="font-size: 48px;"></i>
                    <p class="mt-3">No messages yet. Start the conversation!</p>
                </div>
            @endforelse
        </div>
        <div class="card-footer py-2 px-3 bg-light">
            <form action="{{ route('messages.store') }}" method="POST" class="d-flex gap-2">
                @csrf
                <input type="hidden" name="to_user_id" value="{{ $otherUser->id }}">
                <input type="hidden" name="subject" value="Conversation with {{ $otherUser->first_name }}">
                <input type="text" name="message" class="form-control form-control-sm" placeholder="Type your message..." required>
                <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-send"></i></button>
            </form>
        </div>
    </div>
</div>
@endsection