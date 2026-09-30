    <div class="card-header py-1 px-2 bg-light">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <i class="ti ti-mail text-primary me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Messages</span>
                @if($unreadCount > 0)
                    <span class="badge bg-danger ms-2" style="font-size: 0.6rem;">{{ $unreadCount }} unread</span>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($messages->count() > 0)
            <div class="table-responsive">
                <form id="bulkDeleteForm" action="{{ route('messages.bulk-delete') }}" method="POST">
                    @csrf
                    <table class="table table-hover mb-0" style="font-size: 0.75rem;">
                        <thead class="bg-light">
                            <tr>
                                <th width="30" class="ps-2 py-1">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th class="py-1">From / To</th>
                                <th class="py-1">Subject</th>
                                <th class="py-1">Priority</th>
                                <th class="py-1">Date</th>
                                <th class="pe-2 py-1" width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                                <tr class="{{ !$message->is_read ? 'fw-bold' : '' }}">
                                    <td class="ps-2 py-1">
                                        <input type="checkbox" name="message_ids[]" value="{{ $message->id }}" class="message-checkbox">
                                    </td>
                                    <td class="py-1">
                                        @if($message->sender_id == auth()->id())
                                            {{-- Message sent by current user --}}
                                            @if($message->recipient_ids && count($message->recipient_ids) > 1)
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-users text-primary me-1" title="Sent to multiple recipients" style="font-size: 0.7rem;"></i>
                                                    <span>
                                                        To: {{ count($message->recipient_ids) }} recipients
                                                    </span>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.6rem;">
                                                    {{ $message->recipient_names }}
                                                </small>
                                            @else
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-send text-success me-1" title="Sent message" style="font-size: 0.7rem;"></i>
                                                    <span>
                                                        To: {{ $message->receiver->first_name ?? '' }} {{ $message->receiver->last_name ?? '' }}
                                                    </span>
                                                </div>
                                                @if($message->receiver)
                                                    <small class="text-muted" style="font-size: 0.6rem;">{{ $message->receiver->email ?? '' }}</small>
                                                @endif
                                            @endif
                                        @else
                                            {{-- Message received by current user --}}
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-user text-info me-1" title="From" style="font-size: 0.7rem;"></i>
                                                <span>
                                                    From: {{ $message->sender->first_name ?? '' }} {{ $message->sender->last_name ?? '' }}
                                                </span>
                                            </div>
                                            <small class="text-muted" style="font-size: 0.6rem;">{{ $message->sender->email ?? '' }}</small>
                                        @endif
                                    </td>
                                    <td class="py-1">
                                        <a href="{{ route('messages.show', $message->id) }}" class="text-decoration-none {{ !$message->is_read ? 'fw-bold' : '' }}">
                                            {{ Str::limit($message->subject ?? 'No subject', 50) }}
                                        </a>
                                        @if($message->attachments && $message->attachments->count() > 0)
                                            <i class="ti ti-paperclip ms-1 text-muted" title="Has attachments" style="font-size: 0.7rem;"></i>
                                        @endif
                                        @if($message->priority == 'urgent')
                                            <i class="ti ti-alert text-danger ms-1" title="Urgent" style="font-size: 0.7rem;"></i>
                                        @endif
                                    </td>
                                    <td class="py-1">
                                        {!! $message->priority_badge ?? '<span class="badge bg-secondary" style="font-size: 0.6rem;">Normal</span>' !!}
                                    </td>
                                    <td class="py-1 text-muted">
                                        <div title="{{ $message->created_at->format('Y-m-d H:i:s') }}" style="font-size: 0.65rem;">
                                            {{ $message->created_at->diffForHumans() }}
                                        </div>
                                        @if($message->deadline && $message->deadline->isPast())
                                            <span class="badge bg-warning mt-1" style="font-size: 0.55rem;">Overdue</span>
                                        @endif
                                    </td>
                                    <td class="pe-2 py-1">
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('messages.show', $message->id) }}" class="btn btn-sm btn-outline-primary rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.4rem;" title="View">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('messages.create', ['reply_to' => $message->id]) }}" class="btn btn-sm btn-outline-success rounded-sm" style="font-size: 0.6rem; padding: 0.2rem 0.4rem;" title="Reply">
                                                <i class="ti ti-corner-up-right"></i>
                                            </a>
                                            <button type="button"
                                                    onclick="showDeleteModal({{ $message->id }}, '{{ addslashes($message->subject) }}', null, '{{ route('messages.destroy', $message->id) }}')"
                                                    class="btn btn-sm btn-outline-danger rounded-sm"
                                                    style="font-size: 0.6rem; padding: 0.2rem 0.4rem;"
                                                    title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer py-1 px-2 bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn" style="display: none; font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-trash me-1"></i> Delete Selected
                    </button>
                    <div class="ms-auto">
                        <x-pagination :items="$messages" :perPageOptions="[10, 20, 50, 100]" :ajax="true" />
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="ti ti-inbox" style="font-size: 48px; color: #ccc;"></i>
                <p class="mt-3 text-muted" style="font-size: 0.75rem;">No messages in your inbox</p>
                <a href="{{ route('messages.create') }}" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                    <i class="ti ti-plus me-1"></i> Compose Message
                </a>
            </div>
        @endif
    </div>
