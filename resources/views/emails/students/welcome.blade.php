<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Wearefirst</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; line-height: 1.6; padding: 20px; margin: 0; }
        .wrapper { max-width: 540px; margin: 0 auto; }
        .card { background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; color: #ffffff; font-weight: 900; letter-spacing: -0.5px; }
        .header p { margin: 5px 0 0 0; color: #94a3b8; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 1.5px; }
        .body-content { padding: 40px 30px; }
        .status-badge { display: inline-block; padding: 6px 12px; background-color: #f0fdf4; color: #0284c7; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bae6fd; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 15px; }
        .info-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 25px 0; color: #475569; font-size: 14px; }
        .footer { text-align: center; padding: 30px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>WEAREFIRST</h1>
                <p>Registration Received</p>
            </div>
            
            <div class="body-content">
                <div class="status-badge">Pending Verification</div>
                <div class="greeting">Hello {{ $student->name }},</div>
                <div style="font-size: 12px; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;">
                    Assigned Student ID: WSRN<span style="color: #0284c7; font-size: 14px; background-color: #f0f9ff; padding: 2px 8px; border-radius: 4px; border: 1px solid #bae6fd;">{{ str_pad($student->member_id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
                <p style="color: #475569; font-size: 15px; margin-bottom: 0;">Congratulations on taking your first step! Wearefirst Business Journey Crypto Trading Course Your sponsoring agent has successfully submitted your registration profile into our secure network.</p>

                <div class="info-box">
                    <strong>What happens next?</strong><br>
                    Our Finance Department is currently reviewing your initial admission payment. Once verified, your spot in the network will be officially locked in, and you will receive your official receipt via email.
                </div>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Wearefirst Business Journey. All rights reserved.
        </div>
    </div>
</body>
</html>