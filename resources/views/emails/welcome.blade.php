<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f5f6fa; color: #333; margin: 0; padding: 20px; }
        .card { max-width: 600px; background: #fff; margin: 0 auto; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 30px; line-height: 1.6; }
        .welcome-box { background: #f8f9fa; padding: 20px; border-left: 4px solid #2ecc71; border-radius: 4px; margin: 20px 0; }
        .btn { display: inline-block; padding: 12px 24px; background: #2ecc71; color: #fff; text-decoration: none; border-radius: 5px; font-weight: 500; font-size: 14px; margin-top: 20px; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>Welcome to Smart Complaint System!</h1>
        </div>
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            <p>Thank you for registering an account on our platform! We are excited to help you easily file, track, and resolve any issues or complaints you have.</p>
            
            <div class="welcome-box">
                <p style="margin: 0; font-size: 14px; color: #2c3e50;">
                    Your account (<strong>{{ $user->email }}</strong>) has been successfully created. You can now log in and immediately file complaints, receive notifications, and track updates in real-time.
                </p>
            </div>

            <p>Click the button below to log in and get started:</p>
            
            <center>
                <a href="{{ route('login') }}" class="btn" style="color: #ffffff;">Log In to Your Account</a>
            </center>
        </div>
        <div class="footer">
            <p>This is an automated system notification. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ setting('site_name', 'Smart Complaint System') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
