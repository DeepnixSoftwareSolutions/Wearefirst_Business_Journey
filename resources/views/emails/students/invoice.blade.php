<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Wearefirst Payment Receipt</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; line-height: 1.6; padding: 20px; margin: 0; }
        .wrapper { max-width: 540px; margin: 0 auto; }
        .card { background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; color: #ffffff; font-weight: 900; letter-spacing: -0.5px; }
        .header p { margin: 5px 0 0 0; color: #94a3b8; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 1.5px; }
        .body-content { padding: 40px 30px; }
        .status-badge { display: inline-block; padding: 6px 12px; background-color: #ecfdf5; color: #059669; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #a7f3d0; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 10px; }
        .receipt-table { width: 100%; border-collapse: collapse; margin: 30px 0; background: #f8fafc; border-radius: 16px; overflow: hidden; }
        .receipt-table th, .receipt-table td { padding: 15px 20px; text-align: left; font-size: 14px; border-bottom: 1px solid #e2e8f0; }
        .receipt-table th { color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; }
        .receipt-table td { color: #0f172a; font-weight: 700; }
        .receipt-table .total-row td { background-color: #0f172a; color: #ffffff; font-size: 16px; border-bottom: none; }
        .receipt-table .total-row td:last-child { color: #34d399; }
        .receipt-table .balance-row td { background-color: #ecfdf5; color: #059669; font-size: 14px; border-bottom: none; border-top: 1px solid #a7f3d0; }
        .warning { background-color: #fff1f2; border-left: 4px solid #f43f5e; padding: 15px 20px; border-radius: 0 12px 12px 0; color: #be123c; font-size: 13px; margin-top: 20px; }
        .promo { background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%); border: 1px solid #bae6fd; padding: 20px; border-radius: 16px; color: #0369a1; font-size: 14px; font-weight: 700; text-align: center; margin-top: 20px; box-shadow: inset 0 2px 4px rgba(255,255,255,0.5); }
        .footer { text-align: center; padding: 30px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>WEAREFIRST</h1>
                <p>Official Receipt</p>
            </div>
            
            <div class="body-content">
                @if($isRemainder)
                    <div class="status-badge">Balance Cleared</div>
                    <div class="greeting">Hello {{ $student->name }},</div>
                    <p style="color: #475569; font-size: 14px;">We have successfully received your final payment. Your admission fee is now fully settled and your network nodes are secured!</p>
                @else
                    <div class="status-badge">Payment Received</div>
                    <div class="greeting">Welcome aboard, {{ $student->name }}!</div>
                    <p style="color: #475569; font-size: 14px;">Your initial payment has been verified. You are now officially an Active Student in the network.</p>
                @endif

                <table class="receipt-table">
                    @if(!$isRemainder)
                    <tr>
                        <th>Transaction Ref</th>
                        <td style="text-align: right; font-family: monospace;">{{ $student->payment_transaction_id }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Total Course Fee</th>
                        <td style="text-align: right;">Rs 15,000.00</td>
                    </tr>
                    <tr class="total-row">
                        <td>Amount Paid to Date</td>
                        <td style="text-align: right;">Rs {{ number_format($student->admission_fee_paid, 2) }}</td>
                    </tr>
                    @if($isRemainder)
                    <tr class="balance-row">
                        <td>Remaining Balance</td>
                        <td style="text-align: right;">Rs 0.00</td>
                    </tr>
                    @endif
                </table>

                @if($student->admission_fee_paid < 15000)
                    <div class="warning">
                        <strong>Balance Due: Rs {{ number_format(15000 - $student->admission_fee_paid, 2) }}</strong><br>
                        <span style="font-size: 12px; opacity: 0.9;">Please clear this balance within 14 days to unlock your Agent promotion eligibility.</span>
                    </div>
                @endif

                @if($isRemainder || $student->admission_fee_paid >= 15000)
                    <div class="promo">
                        🚀 You are now eligible to be promoted to an Agent! Your manager will be in touch shortly regarding your training program.
                    </div>
                @endif
            </div>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} Wearefirst Business Journey. All rights reserved.<br>
            If you have questions, please contact your sponsor.
        </div>
    </div>
</body>
</html>