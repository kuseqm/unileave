<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verification Failed | UniLeave</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fc 0%, #e9ecef 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .error-card {
            max-width: 550px;
            background: white;
            border-radius: 24px;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 20px 35px -10px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.2s ease;
        }
        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1.2rem;
            background: #fff5f5;
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-left: auto;
            margin-right: auto;
        }
        h3 {
            color: #1E3A5F;
            margin-bottom: 1rem;
            font-weight: 700;
            font-size: 1.8rem;
        }
        .code-highlight {
            background: #f8f9fa;
            padding: 0.2rem 0.8rem;
            border-radius: 30px;
            font-family: monospace;
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 1px;
            display: inline-block;
            margin: 0.5rem 0;
        }
        p {
            color: #495057;
            margin-bottom: 1.2rem;
            line-height: 1.5;
        }
        .btn-primary {
            background-color: #1E3A5F;
            border: none;
            padding: 0.7rem 1.8rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: #2E5A8A;
            transform: translateY(-2px);
        }
        .try-again {
            margin-top: 1.5rem;
        }
        .try-again a {
            color: #1E3A5F;
            text-decoration: none;
            font-weight: 500;
            border-bottom: 1px dashed #cbd5e0;
        }
        .try-again a:hover {
            border-bottom: 1px solid #1E3A5F;
        }
        .footer-note {
            margin-top: 2rem;
            font-size: 0.75rem;
            color: #868e96;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">
            <i class="ti ti-shield-off"></i>
        </div>
        <h3>Unable to Verify Document</h3>
        <p>The code <span class="code-highlight">{{ $code }}</span> is not recognized by the <strong>UniLeave</strong> system.</p>
        <p>This could mean:<br>
        ✗ The code was typed incorrectly, or<br>
        ✗ The document did not originate from UniLeave.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Return to Home</a>
        <div class="try-again">
            <a href="{{ url('/') }}">Try another verification code</a>
        </div>
        <div class="footer-note">
            <i class="ti ti-lock"></i> Official documents always contain a QR code and an 8‑character code.
        </div>
    </div>
</body>
</html>