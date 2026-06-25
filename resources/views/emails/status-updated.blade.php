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
        .details-box { background: #f8f9fa; padding: 20px; border-left: 4px solid #3498db; border-radius: 4px; margin: 20px 0; }
        .details-item { margin-bottom: 10px; font-size: 14px; }
        .details-item strong { color: #2c3e50; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; color: #fff; font-size: 12px; font-weight: 600; }
        .btn { display: inline-block; padding: 12px 24px; background: #3498db; color: #fff; text-decoration: none; border-radius: 5px; font-weight: 500; font-size: 14px; margin-top: 20px; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>Complaint Status Updated</h1>
        </div>
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            <p>Your complaint status has been updated. Below are the details:</p>
            
            <div class="details-box">
                <div class="details-item"><strong>Complaint ID:</strong> #{{ $complaint->id }}</div>
                <div class="details-item"><strong>Title:</strong> {{ $complaint->title }}</div>
                <div class="details-item">
                    <strong>New Status:</strong> 
                    <span class="status-badge" style="background-color: {{ $complaint->status_color }}">
                        {{ $status }}
                    </span>
                </div>
                @if($comment)
                    <div class="details-item" style="margin-top: 15px;">
                        <strong>Resolution/Update Comment:</strong><br>
                        <i style="color: #777;">"{{ $comment }}"</i>
                    </div>
                @endif
            </div>

            <p>You can check the full timeline and detailed updates by clicking the button below:</p>
            
            <center>
                <a href="{{ route('complaints.show', $complaint->id) }}" class="btn" style="color: #ffffff;">View Complaint Detail</a>
            </center>
        </div>
        <div class="footer">
            <p>This is an automated system notification. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ setting('site_name', 'Smart Complaint System') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
