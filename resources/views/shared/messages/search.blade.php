@extends('layouts.master')

@section('title', 'Search Results')

@section('content')
<div class="container-fluid px-2">
    <x-page-header title="SEARCH RESULTS" description="Found {{ $messages->total() }} messages for '{{ $searchQuery }}'">
        <a href="{{ route('messages.inbox') }}" class="btn btn-outline-secondary btn-sm"><i class="ti ti-arrow-left me-1"></i> Back</a>
    </x-page-header>

    <div class="card rounded-sm">
        <div class="card-body p-0">
            @if($messages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>From/To</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                            <tr>
                                <td>
                                    {{ $message->sender_id == auth()->id() ? 'To: ' . ($message->receiver->first_name ?? '') : 'From: ' . ($message->sender->first_name ?? '') }}
                                </td>
                                <td>{{ $message->subject }}</td>
                                <td>{{ Str::limit($message->message, 50) }}</td>
                                <td class="small">{{ $message->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('messages.show', $message->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 bg-light">{{ $messages->appends(['query' => $searchQuery])->links() }}</div>
            @else
                <div class="text-center py-5">
                    <i class="ti ti-search" style="font-size: 48px;"></i>
                    <p class="mt-3">No messages found for "{{ $searchQuery }}"</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection