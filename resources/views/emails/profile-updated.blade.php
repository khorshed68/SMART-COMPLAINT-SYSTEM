<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f5f6fa; color: #333; margin: 0; padding: 20px; }
        .card { max-width: 600px; background: #fff; margin: 0 auto; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .header { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 30px; line-height: 1.6; }
        .security-box { background: #eff6ff; padding: 20px; border-left: 4px solid #3b82f6; border-radius: 4px; margin: 20px 0; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>Profile Settings Updated</h1>
        </div>
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            <p>This is a quick notification to confirm that your profile details were successfully updated on our system.</p>
            
            <div class="security-box">
                <p style="margin: 0 0 10px 0; font-size: 14px; font-weight: 600; color: #1e3a8a;">Updated Profile details:</p>
                <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #1e40af;">
                    <li><strong>Name</strong>: {{ $user->name }}</li>
                    <li><strong>Email</strong>: {{ $user->email }}</li>
                    <li><strong>Phone</strong>: {{ $user->phone }}</li>
                    <li><strong>Department</strong>: {{ $user->department }}</li>
                </ul>
            </div>

            <p>If you did not authorize this change, please immediately contact the campus IT desk or a system administrator to secure your account.</p>
        </div>
        <div class="footer">
            <p>This is an automated security notification. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ setting('site_name', 'Smart Complaint System') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
