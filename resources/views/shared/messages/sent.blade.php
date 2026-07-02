@extends('layouts.master')

@section('title', 'Sent Messages')

@section('content')
<div class="container-fluid px-2">
    <!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['name' => 'Messages', 'url' => route('messages.inbox')],
    ['name' => 'Sent', 'url' => '#']
]" />

    <x-page-header title="SENT MESSAGES" description="Messages you have sent">
        <a href="{{ route('messages.create') }}" class="btn btn-primary btn-sm"><i class="ti ti-plus me-1"></i> Compose</a>
    </x-page-header>

    <div class="card rounded-sm">
        <div class="card-body p-0">
            @if($messages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>To</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Sent</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                            <tr>
                                <td>{{ $message->receiver->first_name ?? '' }} {{ $message->receiver->last_name ?? '' }}</td>
                                <td>{{ $message->subject }}</td>
                                <td>{{ ucfirst($message->status) }}</td>
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
                <div class="p-3 bg-light">{{ $messages->links() }}</div>
            @else
                <div class="text-center py-5">
                    <i class="ti ti-mail" style="font-size: 48px;"></i>
                    <p class="mt-3">No sent messages</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection