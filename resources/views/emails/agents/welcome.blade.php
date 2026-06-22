<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Promotion Unlocked: Welcome to the Team</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; line-height: 1.6; padding: 20px; margin: 0; }
        .wrapper { max-width: 540px; margin: 0 auto; }
        .card { background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; color: #ffffff; font-weight: 900; letter-spacing: -0.5px; }
        .header p { margin: 5px 0 0 0; color: #94a3b8; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 1.5px; }
        .body-content { padding: 40px 30px; }
        .status-badge { display: inline-block; padding: 6px 12px; background-color: #f5f3ff; color: #6d28d9; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd6fe; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 10px; }
        .credentials-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 25px; margin: 25px 0; }
        .credentials-box h3 { margin: 0 0 15px 0; font-size: 14px; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
        .cred-row { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding: 12px 0; font-size: 14px; }
        .cred-row:last-child { border-bottom: none; }
        .cred-label { color: #64748b; font-weight: 600; }
        .cred-value { color: #0f172a; font-weight: 700; }
        .cred-value a { color: #4f46e5; text-decoration: none; }
        .code-block { background-color: #e2e8f0; padding: 4px 8px; border-radius: 6px; font-family: monospace; font-size: 13px; color: #0f172a; letter-spacing: 1px; }
        .security-notice { background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px 20px; border-radius: 0 12px 12px 0; color: #b45309; font-size: 13px; margin-top: 20px; }
        .footer { text-align: center; padding: 30px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>WEAREFIRST</h1>
                <p>Status Update</p>
            </div>
            
            <div class="body-content">
                <div class="status-badge">Promotion Unlocked</div>
                <div class="greeting">Congratulations, {{ $user->name }}!</div>
                <p style="color: #475569; font-size: 14px;">You have successfully completed your training protocol and have been officially promoted to an <strong>Agent</strong>.</p>
                <p style="color: #475569; font-size: 14px;">Your 3 Network Nodes (Main, Sub A, and Sub B) are now fully operational. You can now log into the portal to manage your network, track your recruits, and monitor your commissions.</p>

                <div class="credentials-box">
                    <h3>System Credentials</h3>
                    <div class="cred-row">
                        <span class="cred-label">Member ID</span>
                        <span class="cred-value" style="color: #6d28d9; font-size: 16px;">{{ str_pad($user->member_id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="cred-row">
                        <span class="cred-label">Secure Portal</span>
                        <span class="cred-value"><a href="{{ url('/login') }}">Login Here &rarr;</a></span>
                    </div>
                    <div class="cred-row">
                        <span class="cred-label">Email Address</span>
                        <span class="cred-value">{{ $user->email }}</span>
                    </div>
                    <div class="cred-row">
                        <span class="cred-label">Temp Password</span>
                        <span class="code-block">{{ $rawPassword }}</span>
                    </div>
                </div>

                <div class="security-notice">
                    <strong>Security Requirement:</strong> Please log in and change your temporary password immediately from your Profile Settings.
                </div>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Wearefirst Management. All rights reserved.<br>
            Welcome to the core team.
        </div>
    </div>
</body>
</html>