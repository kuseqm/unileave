@props(['leaveRequest', 'staff', 'additionalNotes' => null])

@php
    $startDate = \Carbon\Carbon::parse($leaveRequest->start_date);
    $endDate = \Carbon\Carbon::parse($leaveRequest->end_date);
    $expectedReturn = $leaveRequest->expected_return_date ? \Carbon\Carbon::parse($leaveRequest->expected_return_date) : null;
    $actingStaff = $leaveRequest->actingStaff;

    // SJUT's own convention: the opening paragraph is unnumbered (just
    // first-line indented like a typewritten letter); every substantive
    // paragraph after it is numbered starting at 2.
    $n = 2;
@endphp

<div class="letter-body" style="font-family: Arial, Helvetica, sans-serif; font-size: 12pt; line-height: 1.2; text-align: justify;">

    <!-- Salutation -->
    <p style="margin-bottom: 4px;">Dear <strong>{{ $staff->first_name ?? 'Staff' }}</strong>,</p>

    <!-- Opening Statement (unnumbered, first-line indented) -->
    <p style="margin-bottom: 8px; text-indent: 2.5em;">We are pleased to inform you that your leave request has been <strong style="color: #2e7d32;">APPROVED</strong> as follows:</p>

    <!-- Leave Details Table - Compact -->
    <table style="width: 100%; border-collapse: collapse; margin: 5px 0; font-size: 10pt; page-break-inside: avoid;">
        <tr>
            <td style="padding: 3px; border: 1px solid #ddd; background: #f5f5f5; width: 35%;">
                <strong>Leave Type:</strong>
            </td>
            <td style="padding: 3px; border: 1px solid #ddd;">
                {{ $leaveRequest->leaveType->name ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td style="padding: 3px; border: 1px solid #ddd; background: #f5f5f5;">
                <strong>Start Date:</strong>
            </td>
            <td style="padding: 3px; border: 1px solid #ddd;">
                {{ $startDate->format('l, jS F Y') }}
            </td>
        </tr>
        <tr>
            <td style="padding: 3px; border: 1px solid #ddd; background: #f5f5f5;">
                <strong>End Date:</strong>
            </td>
            <td style="padding: 3px; border: 1px solid #ddd;">
                {{ $endDate->format('l, jS F Y') }}
            </td>
        </tr>
        <tr>
            <td style="padding: 3px; border: 1px solid #ddd; background: #f5f5f5;">
                <strong>Total Days:</strong>
            </td>
            <td style="padding: 3px; border: 1px solid #ddd;">
                {{ $leaveRequest->days }} day(s)
                @if($leaveRequest->half_day && $leaveRequest->half_day !== 'full')
                    ({{ ucfirst($leaveRequest->half_day) }} half day)
                @endif
            </td>
        </tr>
        @if($expectedReturn)
        <tr>
            <td style="padding: 3px; border: 1px solid #ddd; background: #f5f5f5;">
                <strong>Expected Return Date:</strong>
            </td>
            <td style="padding: 3px; border: 1px solid #ddd;">
                {{ $expectedReturn->format('l, jS F Y') }}
            </td>
        </tr>
        @endif
        <tr>
            <td style="padding: 3px; border: 1px solid #ddd; background: #f5f5f5;">
                <strong>Acting Staff:</strong>
            </td>
            <td style="padding: 3px; border: 1px solid #ddd;">
                <strong>{{ $actingStaff->full_name ?? 'N/A' }}</strong>
            </td>
        </tr>
    </table>
    
    {{-- Every numbered paragraph below gives the number its own fixed-width
         column (an inline-block span), so the text after it always starts
         at the same tab stop whether the number is "2." or "10." - a plain
         run of &nbsp; couldn't do that, since "10." simply takes more room
         than "2.". A wrapped second line is deliberately NOT indented to
         match - it returns to the paragraph's own left edge, same as the
         opening paragraph above. --}}
    @php $numberColumn = 'display: inline-block; width: 2.2em; vertical-align: top;'; @endphp

    <!-- Acting Staff Acknowledgment -->
    <p style="margin: 8px 0; page-break-inside: avoid;"><span style="{{ $numberColumn }}">{{ $n++ }}.</span>{{ $actingStaff->full_name ?? 'The designated staff member' }} has graciously accepted to hold your position during your absence. Please ensure a thorough handover before proceeding on leave.</p>

    <!-- Return Reminder -->
    <p style="margin: 8px 0; page-break-inside: avoid;"><span style="{{ $numberColumn }}">{{ $n++ }}.</span>Kindly note that you are expected to resume duties on <strong>{{ $expectedReturn ? $expectedReturn->format('l, jS F Y') : $endDate->copy()->addDay()->format('l, jS F Y') }}</strong>. Timely return allows the acting staff to transition back to their regular duties.</p>

    <!-- Conditions -->
    <p style="margin: 8px 0; page-break-inside: avoid;"><span style="{{ $numberColumn }}">{{ $n++ }}.</span><strong>Conditions of Leave:</strong> Ensure proper handover of duties to the acting staff before proceeding on leave. Provide a valid contact number where you can be reached during the leave period. In case of emergency, you may be recalled and your leave days adjusted accordingly. Upon return, submit a leave report to your Head of Department within 7 days. Maintain communication with your department for any urgent matters.</p>

    <!-- Additional Notes -->
    @if($additionalNotes)
        <p style="margin: 8px 0;"><span style="{{ $numberColumn }}">{{ $n++ }}.</span><strong>Additional Notes:</strong> {{ $additionalNotes }}</p>
    @endif

    <!-- Closing -->
    <p style="margin-top: 8px; margin-bottom: 0; page-break-inside: avoid;"><span style="{{ $numberColumn }}">{{ $n++ }}.</span>We wish you a restful and enjoyable leave.</p>
    
</div>