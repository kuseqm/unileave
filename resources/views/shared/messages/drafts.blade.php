@extends('layouts.master')

@section('title', 'Draft Messages')

@section('content')
<div class="container-fluid px-2">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Messages', 'url' => '#'],
        ['name' => 'Draft', 'url' => '#']
    ]" />
    <x-page-header title="DRAFTS" description="Saved draft messages">
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
                                <th>Last Modified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                            <tr>
                                <td>{{ $message->receiver->first_name ?? '' }} {{ $message->receiver->last_name ?? '' }}</td>
                                <td>{{ $message->subject }}</td>
                                <td class="small">{{ $message->updated_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('messages.create', ['reply_to' => $message->id]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-edit"></i>
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
                    <i class="ti ti-file" style="font-size: 48px;"></i>
                    <p class="mt-3">No draft messages</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection