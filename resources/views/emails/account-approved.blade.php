<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f5f6fa; color: #333; margin: 0; padding: 20px; }
        .card { max-width: 600px; background: #fff; margin: 0 auto; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 30px; line-height: 1.6; }
        .approved-box { background: #f0fdf4; padding: 20px; border-left: 4px solid #10b981; border-radius: 4px; margin: 20px 0; }
        .btn { display: inline-block; padding: 12px 24px; background: #10b981; color: #fff; text-decoration: none; border-radius: 5px; font-weight: 500; font-size: 14px; margin-top: 20px; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>Account Approved!</h1>
        </div>
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            <p>Great news! A system administrator has reviewed and **approved** your registration request.</p>
            
            <div class="approved-box">
                <p style="margin: 0; font-size: 14px; color: #065f46;">
                    Your account (<strong>{{ $user->email }}</strong>) is now fully active. You can now log in to file campus complaints, check timelines, and participate on the bulletins board.
                </p>
            </div>

            <p>Click the button below to log in and access your workspace dashboard:</p>
            
            <center>
                <a href="{{ route('login') }}" class="btn" style="color: #ffffff;">Log In to Dashboard</a>
            </center>
        </div>
        <div class="footer">
            <p>This is an automated system notification. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ setting('site_name', 'Smart Complaint System') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
