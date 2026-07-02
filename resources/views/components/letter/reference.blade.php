@props([
    'referenceNumber' => null,
    'staffId' => null,
    'secureCode' => null,
    'date' => null
])

@php
    /**
     * Format date with superscript ordinal (1st, 2nd, 3rd, 4th)
     */
    function formatDateWithSuperscript($date)
    {
        $carbon = $date ? \Carbon\Carbon::parse($date) : now();
        $day = $carbon->format('j');
        $month = $carbon->format('F');
        $year = $carbon->format('Y');
        $weekday = $carbon->format('l');
        
        // Determine ordinal suffix
        if ($day == 1 || $day == 21 || $day == 31) {
            $ordinal = 'st';
        } elseif ($day == 2 || $day == 22) {
            $ordinal = 'nd';
        } elseif ($day == 3 || $day == 23) {
            $ordinal = 'rd';
        } else {
            $ordinal = 'th';
        }
        
        // Return HTML with superscript ordinal
        return $weekday . ', ' . $day . '<sup style="font-size: 0.65em; line-height: 0; vertical-align: super;">' . $ordinal . '</sup> ' . $month . ' ' . $year;
    }
@endphp

<div class="letter-reference" style="font-family: 'Times New Roman', Times, serif; margin-bottom: 20px;">
    
    <!-- Row 1: Our Ref (left) + Date (right) -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
        <div>
            <strong>Our Ref:</strong> {{ $referenceNumber ?? 'N/A' }}
        </div>
        <div>
            <strong>Date:</strong> 
            {!! $date ? formatDateWithSuperscript($date) : formatDateWithSuperscript(null) !!}
        </div>
    </div>
    
    <!-- Row 2: Your Ref (left) + Letter Unique Code (right) -->
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <strong>Your Ref:</strong> 
            @if($staffId)
                {{ $staffId }}
            @else
                <span style="color: #999;">—</span>
            @endif
        </div>
        <div>
            <strong>Letter Unique Code:</strong> 
            @if($secureCode)
                <span style="background: #f0f0f0; padding: 2px 5px; font-family: monospace; font-size: 9pt; letter-spacing: 1px;">
                    {{ $secureCode }}
                </span>
            @else
                <span style="color: #999;">—</span>
            @endif
        </div>
    </div>
    
</div>