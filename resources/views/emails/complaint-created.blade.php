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
        .btn { display: inline-block; padding: 12px 24px; background: #3498db; color: #fff; text-decoration: none; border-radius: 5px; font-weight: 500; font-size: 14px; margin-top: 20px; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>{{ $isAdmin ? 'New Complaint Submitted' : 'Complaint Submitted Successfully' }}</h1>
        </div>
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            @if($isAdmin)
                <p>A new complaint has been filed in the system and requires review.</p>
            @else
                <p>Thank you for submitting your complaint. We have received it and will look into it shortly.</p>
            @endif
            
            <div class="details-box">
                <div class="details-item"><strong>Complaint ID:</strong> #{{ $complaint->id }}</div>
                <div class="details-item"><strong>Title:</strong> {{ $complaint->title }}</div>
                <div class="details-item"><strong>Category:</strong> {{ $complaint->category->name ?? 'Other' }}</div>
                <div class="details-item"><strong>Priority:</strong> {{ $complaint->priority }}</div>
                <div class="details-item"><strong>Description:</strong> {{ Str::limit($complaint->description, 150) }}</div>
            </div>

            <p>You can track the progress of this complaint in real-time by clicking the button below:</p>
            
            <center>
                @if($isAdmin)
                    <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="btn" style="color: #ffffff;">View Complaint Detail</a>
                @else
                    <a href="{{ route('complaints.show', $complaint->id) }}" class="btn" style="color: #ffffff;">View Complaint Detail</a>
                @endif
            </center>
        </div>
        <div class="footer">
            <p>This is an automated system notification. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ setting('site_name', 'Smart Complaint System') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
