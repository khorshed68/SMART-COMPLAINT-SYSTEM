<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f5f6fa; color: #333; margin: 0; padding: 20px; }
        .card { max-width: 600px; background: #fff; margin: 0 auto; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .header { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 30px; line-height: 1.6; }
        .alert-box { background: #fef2f2; padding: 20px; border-left: 4px solid #ef4444; border-radius: 4px; margin: 20px 0; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>Security Alert: Password Changed</h1>
        </div>
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            <p>This is a security alert to notify you that the password for your account has been successfully changed.</p>
            
            <div class="alert-box">
                <p style="margin: 0; font-size: 14px; color: #991b1b; font-weight: 500;">
                    If you initiated this change, no further action is required. Your new password is now active.
                </p>
            </div>

            <p style="color: #4b5563; font-size: 14px;">
                <strong>WARNING:</strong> If you did not request this password change, someone else may have gained access to your account. Please log in using password recovery options immediately or contact the IT support helpdesk.
            </p>
        </div>
        <div class="footer">
            <p>This is an automated security notification. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ setting('site_name', 'Smart Complaint System') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
