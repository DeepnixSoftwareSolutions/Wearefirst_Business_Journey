<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Reminder: Balance Due</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; line-height: 1.6; padding: 20px; margin: 0; }
        .wrapper { max-width: 540px; margin: 0 auto; }
        .card { background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; color: #ffffff; font-weight: 900; letter-spacing: -0.5px; }
        .header p { margin: 5px 0 0 0; color: #94a3b8; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 1.5px; }
        .body-content { padding: 40px 30px; }
        .status-badge { display: inline-block; padding: 6px 12px; background-color: #fff1f2; color: #e11d48; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecdd3; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 10px; }
        .balance-box { text-align: center; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 25px; margin: 25px 0; }
        .balance-label { font-size: 11px; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 1px; }
        .balance-amount { font-size: 32px; font-weight: 900; color: #0f172a; margin-top: 5px; }
        .critical-warning { background-color: #fff1f2; border-left: 4px solid #e11d48; padding: 20px; border-radius: 0 12px 12px 0; color: #9f1239; font-size: 13px; line-height: 1.5; }
        .footer { text-align: center; padding: 30px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>WEAREFIRST</h1>
                <p>Action Required</p>
            </div>
            
            <div class="body-content">
                <div class="status-badge">Payment Overdue</div>
                <div class="greeting">Dear {{ $user->name }},</div>
                <div style="font-size: 12px; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;">
                    Account Reference ID: <span style="color: #e11d48; font-size: 14px; background-color: #fff1f2; padding: 2px 8px; border-radius: 4px; border: 1px solid #fecdd3;">{{ str_pad($user->member_id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
                <p style="color: #475569; font-size: 14px;">This is an automated notice from the Finance Department. It has been 10 days since your initial registration into the network.</p>

                <div class="balance-box">
                    <div class="balance-label">Outstanding Balance</div>
                    <div class="balance-amount">Rs {{ number_format($balanceDue, 2) }}</div>
                </div>

                <div class="critical-warning">
                    <strong>Final Cut-off Warning:</strong> You have exactly <strong>4 days remaining</strong> to complete this payment. Failure to clear this balance within the 14-day window will result in your account being placed on OVERDUE status, instantly freezing your network nodes.
                </div>

                <p style="color: #475569; font-size: 14px; margin-top: 25px; font-weight: 600;">Please contact your Agent or our support team immediately to finalize your payment.</p>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Wearefirst Business Journey. All rights reserved.
        </div>
    </div>
</body>
</html>