@props(['leaveRequest', 'staff', 'recallReason', 'daysHandled', 'balanceHandling'])

<div class="letter-body" style="font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.5;">
    <p>Dear <strong>{{ $staff->first_name ?? 'Staff' }}</strong>,</p>
    
    <p>This letter serves to <strong style="color: #ff6600;">RECALL</strong> you from your ongoing leave due to the following emergency:</p>
    
    <div style="background: #fff3e0; padding: 10px; margin: 15px 0; border-left: 3px solid #ff6600;">
        <strong>Reason for Recall:</strong><br>
        {{ $recallReason }}
    </div>
    
    <p><strong>Leave Days Handling:</strong></p>
    <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9; width: 50%;">
                <strong>Days Used Before Recall:</strong>
            </td>
            <td style="padding: 8px; border: 1px solid #ddd;">
                {{ $daysHandled ?? 'To be calculated' }} day(s)
            </td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9;">
                <strong>Selected Option:</strong>
            </td>
            <td style="padding: 8px; border: 1px solid #ddd;">
                @if($balanceHandling === 'pause')
                    Pause remaining days (you can take them later)
                @elseif($balanceHandling === 'deduct')
                    Deduct only used days (remaining days refunded)
                @else
                    Full leave counted as used (company pays)
                @endif
            </td>
        </tr>
    </table>
    
    <p>Please report back to your Head of Department immediately upon receipt of this letter.</p>
    
    <p>We understand this may be inconvenient and thank you for your understanding.</p>
</div>