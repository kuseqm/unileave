@extends('layouts.guest')

@section('title', 'Verify Document')

@section('content')
<div class="card card-verify">
    <div class="card-header">
        <i class="ti ti-qrcode"></i> Document Verification
    </div>
    <div class="card-body p-4">
        <p>Enter the 8‑character verification code from your document.</p>
        <form id="verifyForm" method="GET">
            <div class="mb-3">
                <input type="text" class="form-control form-control-lg" id="code" name="code" 
                       placeholder="e.g., A3B9K7X2" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="ti ti-search"></i> Verify
            </button>
        </form>
        <small class="text-muted mt-3 d-block">* The code can be found on the PDF report or leave letter, usually near the QR code.</small>
    </div>
</div>

<script>
    document.getElementById('verifyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let code = document.getElementById('code').value.trim();
        if (code) {
            window.location.href = '/verify/' + code;
        } else {
            alert('Please enter a verification code.');
        }
    });
</script>
@endsection