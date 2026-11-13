<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt - {{ $payment->reference_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            color: #111111;
            padding: 30px 15px;
        }
        .receipt-card {
            max-width: 650px;
            margin: auto;
            background: #ffffff;
            border: 2px solid #111111;
            border-radius: 0;
            padding: 35px;
            box-shadow: none;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #111111;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .header h2 {
            margin: 0 0 5px;
            color: #111111;
            font-size: 22px;
            text-transform: uppercase;
        }
        .header p {
            margin: 3px 0;
            color: #111111;
            font-size: 13px;
        }
        .or-number-badge {
            display: inline-block;
            background: #ffffff;
            color: #111111;
            padding: 6px 16px;
            border-radius: 0;
            font-weight: bold;
            font-size: 14px;
            margin-top: 10px;
            border: 1px solid #111111;
        }
        .info-grid {
            margin-bottom: 25px;
            font-size: 14px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #111111;
        }
        .row span:first-child {
            color: #111111;
        }
        .row span:last-child {
            font-weight: 600;
        }
        .divider {
            border-top: 2px dashed #111111;
            margin: 20px 0;
        }
        .amount-box {
            background: #ffffff;
            border: 2px solid #111111;
            border-radius: 0;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
        }
        .amount-title {
            font-size: 13px;
            color: #111111;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .amount-val {
            font-size: 36px;
            font-weight: 800;
            color: #111111;
        }
        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
        }
        .sig-box {
            text-align: center;
            width: 200px;
        }
        .sig-line {
            border-top: 1px solid #111111;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 12px;
            font-weight: bold;
        }
        .print-btn {
            display: inline-block;
            background: #ffffff;
            color: #111111;
            padding: 10px 20px;
            border-radius: 0;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 20px;
            cursor: pointer;
            border: 1px solid #111111;
        }
        @media print {
            body { background: white; padding: 0; }
            .receipt-card { border: 2px solid #111111; box-shadow: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div style="max-width: 650px; margin: auto; text-align: right;" class="no-print">
        <button onclick="window.print()" class="print-btn">🖨️ Print Receipt</button>
        <a href="{{ route('payments.index') }}" class="print-btn" style="background: #ffffff; color: #111111;">Back to Payments</a>
    </div>

    <div class="receipt-card">
        <div class="header">
            <h2>{{ $barangayName }}</h2>
            <p>Community Water Service &bull; Juban, Sorsogon</p>
            <p><strong>OFFICIAL CASH RECEIPT</strong></p>
            <div class="or-number-badge">OR # {{ $payment->reference_number }}</div>
        </div>

        <div class="info-grid">
            <div class="row">
                <span>Received From (Consumer):</span>
                <span>{{ $payment->consumer ? $payment->consumer->full_name : ($payment->bill->consumer->full_name ?? 'N/A') }}</span>
            </div>
            <div class="row">
                <span>Account Number:</span>
                <span>{{ $payment->consumer->account_number ?? ($payment->bill->consumer->account_number ?? 'N/A') }}</span>
            </div>
            <div class="row">
                <span>Service Address:</span>
                <span>{{ $payment->consumer->address ?? ($payment->bill->consumer->address ?? 'Brgy. Caruhayon') }}</span>
            </div>
            <div class="row">
                <span>For Water Bill Statement:</span>
                <span>{{ $payment->bill->bill_number }} ({{ $payment->bill->billing_period }})</span>
            </div>
            <div class="row">
                <span>Water Consumption:</span>
                <span>{{ number_format($payment->bill->consumption, 2) }} m³</span>
            </div>
            <div class="row">
                <span>Payment Date:</span>
                <span>{{ $payment->payment_date->format('F d, Y') }}</span>
            </div>
            <div class="row">
                <span>Payment Method:</span>
                <span>{{ $payment->payment_method }} (Over-the-Counter)</span>
            </div>
            <div class="row">
                <span>Remarks / Notes:</span>
                <span>{{ $payment->notes ?? 'Settled in full at Barangay Hall' }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="amount-box">
            <div class="amount-title">Total Amount Received</div>
            <div class="amount-val">₱{{ number_format($payment->amount_paid, 2) }}</div>
            <small style="color: #111111;">Philippine Peso (PHP)</small>
        </div>

        <div class="signature-area">
            <div class="sig-box">
                <div class="sig-line">Consumer Signature</div>
            </div>
            <div class="sig-box">
                <div class="sig-line">Barangay Treasurer / Collecting Officer</div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #111111; border-top: 2px dashed #111111; padding-top: 12px;">
            Thank you for supporting Barangay Caruhayon Community Water Service! &bull; Helpline: {{ $systemContact }}
        </div>
    </div>
</body>
</html>
