@props(['staff' => null, 'customAddress' => null, 'multipleRecipients' => null])

<div class="letter-recipient" style="font-family: 'Times New Roman', Times, serif; margin-bottom: 20px;">
    
    @if($multipleRecipients && count($multipleRecipients) > 0)
        <!-- Multiple Recipients -->
        <div style="margin: 0; line-height: 1.6; font-size: 11pt;">
            <strong>To:</strong><br>
            @foreach($multipleRecipients as $recipient)
                @if(is_array($recipient) && isset($recipient['name']))
                    {{ $recipient['name'] }}<br>
                    @if(isset($recipient['address']))
                        <span style="margin-left: 15px; font-size: 10pt; color: #555;">{{ $recipient['address'] }}</span><br>
                    @endif
                @else
                    {{ $recipient }}<br>
                @endif
            @endforeach
        </div>
        
    @elseif($customAddress)
        <!-- Custom Address (External Recipient) -->
        <div style="margin: 0; line-height: 1.6; font-size: 11pt;">
            {!! nl2br(e($customAddress)) !!}
        </div>
        
    @elseif($staff)
        <!-- Single Staff Recipient -->
        <p style="margin: 0; line-height: 1.6; font-size: 11pt;">
            <strong>To:</strong> {{ $staff->full_name ?? 'N/A' }}<br>
            <strong>Staff ID:</strong> {{ $staff->staff_id ?? 'N/A' }}<br>
            <strong>Department:</strong> {{ $staff->department->name ?? 'N/A' }}<br>
            <strong>Position:</strong> {{ $staff->position ?? 'N/A' }}
        </p>
        
    @else
        <!-- No recipient information -->
        <p style="margin: 0; line-height: 1.6; font-size: 11pt; color: #999;">
            <strong>To:</strong> [Recipient information not available]
        </p>
    @endif
    
</div>