@props(['content'])

<div class="letter-body" style="font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.5;">
    <p>Dear <strong>Staff Member</strong>,</p>
    
    <div style="margin: 15px 0;">
        {!! $content !!}
    </div>
    
    <p>Should you have any questions, please do not hesitate to contact the Human Resources Department.</p>
</div>