@props([
    'signatory' => null,
    'signatoryTitle' => null,
    'cc' => [],
    'signedAt' => null,
    'signedByIp' => null,
    'isDigitallySigned' => false,
    'signatureImage' => null,
])

<div class="letter-signature" style="font-family: Arial, Helvetica, sans-serif; margin-top: 10px; font-size: 11pt;">

    <!-- Signature Block -->
    <p style="margin-bottom: 3px;">Yours sincerely,</p>

    <div style="margin-top: 12px;">
        @if($signatureImage)
            <img src="{{ $signatureImage }}" alt="Signature" style="height: 45px; margin-bottom: 2px;">
        @endif
        @if($signatory)
            <p style="margin: 0; line-height: 1.3;"><strong>{{ $signatory }}</strong></p>
            <p style="margin: 0; line-height: 1.3;">{{ $signatoryTitle ?? 'Human Resources Department' }}</p>
        @else
            <p style="margin: 0; line-height: 1.3;"><strong>Human Resources Department</strong></p>
        @endif
    </div>
    
    <!-- Digital Signature Info (only for signed letters) -->
    @if($isDigitallySigned && $signedAt)
        <div style="margin-top: 15px; padding-top: 8px; border-top: 1px solid #ccc; font-size: 8pt; color: #555;">
            <p style="margin: 2px 0;">
                <i class="ti ti-check-circle" style="color: #2e7d32;"></i> 
                Digitally signed by {{ $signatory ?? 'Authorized Signatory' }} on {{ \Carbon\Carbon::parse($signedAt)->format('l, jS F Y \a\t H:i:s') }}
            </p>
            @if($signedByIp)
                <p style="margin: 2px 0; font-size: 7.5pt;">
                    <i class="ti ti-shield"></i> 
                    Signature IP: {{ $signedByIp }}
                </p>
            @endif
            <p style="margin: 2px 0; font-size: 7.5pt;">
                <i class="ti ti-lock"></i> 
                This is a legally binding digital signature
            </p>
        </div>
    @endif
    
    <!-- CC List -->
    @if(count($cc) > 0)
        <div style="margin-top: 10px; font-size: 8.5pt;">
            <strong>CC:</strong>
            <ul style="margin: 3px 0 0 20px; padding: 0;">
                @foreach($cc as $recipient)
                    <li style="margin-bottom: 2px;">{{ $recipient }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
</div>