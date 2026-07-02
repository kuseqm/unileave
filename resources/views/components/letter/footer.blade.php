@props([
    'verificationCode' => null, 
    'verificationUrl' => null,
    'printedAt' => null,
    'pageNumber' => null,
    'totalPages' => null
])

<div class="letter-footer" style="font-family: 'Times New Roman', Times, serif; margin-top: 25px; border-top: 1px solid #ccc; padding-top: 8px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 15px;">
        
        <!-- Left Side: Text Information -->
        <div style="flex: 2;">
            <p style="margin: 0; font-size: 7.5pt; color: #555;">
                <!--This is a computer-generated letter. No physical signature is required.-->
            </p>
            @if($verificationUrl)
            <p style="margin: 4px 0 0 0; font-size: 7pt;">
                <i class="ti ti-shield-check"></i> 
                Verify authenticity: 
                <a href="{{ $verificationUrl }}" style="color: #2e7d32; text-decoration: none;" target="_blank">
                    {{ $verificationUrl }}
                </a>
            </p>
            @endif
            @if($printedAt)
            <p style="margin: 4px 0 0 0; font-size: 6.5pt; color: #999;">
                <i class="ti ti-printer"></i> 
                Printed on: {{ \Carbon\Carbon::parse($printedAt)->format('d M Y H:i:s') }}
            </p>
            @endif
        </div>
        
        <!-- Right Side: QR Code -->
        @if($verificationCode)
        <div style="flex-shrink: 0; text-align: center;">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate($verificationUrl ?? $verificationCode) !!}
            <p style="margin: 2px 0 0 0; font-size: 6pt; color: #888;">Scan to verify</p>
        </div>
        @endif
        
    </div>
    
    <!-- Page Number (if provided) -->
    @if($pageNumber)
    <div style="text-align: center; margin-top: 5px; font-size: 7pt; color: #999;">
        Page {{ $pageNumber }} @if($totalPages) of {{ $totalPages }} @endif
    </div>
    @endif
    
</div>