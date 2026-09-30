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
            <div class="p-3 bg-light">
                <x-pagination :items="$messages" :perPageOptions="[10, 20, 50, 100]" :ajax="true" />
            </div>
        @else
            <div class="text-center py-5">
                <i class="ti ti-file" style="font-size: 48px;"></i>
                <p class="mt-3">No draft messages</p>
            </div>
        @endif
    </div>
