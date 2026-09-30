<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background:#fdf8f4; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fdf8f4; padding: 24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="background:#ffffff; border:1px solid #E8E2D5; border-radius: 4px;">
                    <tr>
                        <td style="padding: 24px 28px 8px 28px;">
                            <div style="font-size: 18px; font-weight: 700; color:#1E293B;">
                                Staff<span style="color:#3B82F6;">MS</span>
                            </div>
                            <div style="font-size: 11px; letter-spacing: 1px; color:#64748B; text-transform: uppercase;">
                                Staff Management System
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 16px 28px 0 28px; font-size: 14px; color:#1E293B;">
                            <p style="margin: 0 0 12px 0;">Hello {{ $staff->first_name }},</p>
                            <p style="margin: 0 0 16px 0; color:#475569;">
                                An account has been created for you on the Staff Management System.
                                Use the credentials below to activate it by logging in and setting your own password.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fdf8f4; border:1px solid #E8E2D5; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 12px 16px; font-size: 13px; color:#64748B; width: 40%;">Staff ID</td>
                                    <td style="padding: 12px 16px; font-size: 13px; color:#1E293B; font-weight: 600;">{{ $staff->staff_id }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 16px 12px 16px; font-size: 13px; color:#64748B;">Email</td>
                                    <td style="padding: 0 16px 12px 16px; font-size: 13px; color:#1E293B; font-weight: 600;">{{ $staff->email }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 16px 12px 16px; font-size: 13px; color:#64748B;">Temporary password</td>
                                    <td style="padding: 0 16px 12px 16px; font-size: 13px; color:#1E293B; font-weight: 600;">{{ $temporaryPassword }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 28px 0 28px;">
                            <a href="{{ route('login') }}" style="display:inline-block; background:#1E293B; color:#ffffff; text-decoration:none; font-size: 13px; font-weight: 600; padding: 10px 20px; border-radius: 4px;">
                                Log in to activate your account
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 28px 24px 28px; font-size: 12px; color:#94A3B8;">
                            You'll be asked to set your own password the first time you log in. If you weren't expecting this account, please contact HR.
                        </td>
                    </tr>
                </table>
                <div style="font-size: 11px; color:#94A3B8; padding: 16px 0;">
                    &copy; {{ date('Y') }} Staff Management System
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
