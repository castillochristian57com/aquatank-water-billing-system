<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Water Statement - {{ $bill->bill_number }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            padding: 30px 15px;
            margin: 0;
        }
        .action-bar {
            max-width: 780px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .print-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #0284c7;
            color: white;
            padding: 9px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 13.5px;
            cursor: pointer;
            border: none;
            transition: background 0.2s;
        }
        .print-btn:hover { background: #0369a1; }
        .btn-secondary { background: #64748b; }
        .btn-secondary:hover { background: #475569; }
        .statement-card {
            max-width: 780px;
            margin: auto;
            background: white;
            border: 2px solid #0284c7;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            box-sizing: border-box;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 18px;
            margin-bottom: 25px;
            gap: 15px;
        }
        .header-logo {
            width: 68px;
            height: 68px;
            object-fit: contain;
        }
        .header-center {
            text-align: center;
            flex-grow: 1;
        }
        .header-center h3 {
            margin: 0;
            color: #0284c7;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header-center h1 {
            margin: 3px 0;
            color: #0f172a;
            font-size: 22px;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header-center p {
            margin: 2px 0;
            color: #64748b;
            font-size: 12.5px;
        }
        .header-center .statement-badge {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            font-weight: bold;
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 20px;
            margin-top: 6px;
            letter-spacing: 0.5px;
            border: 1px solid #bae6fd;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
            font-size: 13.5px;
        }
        .meta-box {
            background: #f8fcff;
            border: 1px solid #e0f2fe;
            border-radius: 8px;
            padding: 16px;
        }
        .meta-title {
            font-weight: bold;
            color: #0369a1;
            margin-bottom: 10px;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 7px;
        }
        .row span:first-child { color: #64748b; }
        .row span:last-child { font-weight: 600; color: #0f172a; }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .status-unpaid { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .status-overdue { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 22px 0;
            font-size: 13.5px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            text-align: left;
        }
        th {
            background: #e0f2fe;
            color: #0369a1;
            font-weight: bold;
            font-size: 12.5px;
            text-transform: uppercase;
        }
        .total-box {
            background: #eff6ff;
            border: 2px solid #93c5fd;
            border-radius: 8px;
            padding: 18px 24px;
            text-align: right;
            margin-bottom: 25px;
        }
        .total-box .label {
            font-size: 12px;
            color: #475569;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .total-box .amount {
            font-size: 32px;
            font-weight: 800;
            color: #1d4ed8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .payment-summary {
            font-size: 13px;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #bfdbfe;
        }
        .footer-note {
            font-size: 11.5px;
            color: #64748b;
            border-top: 1px dashed #cbd5e1;
            padding-top: 15px;
            text-align: center;
            line-height: 1.5;
        }
        @media print {
            body { background: white; padding: 0; }
            .statement-card { border: none; box-shadow: none; padding: 0; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="action-bar no-print">
        <a href="{{ route('portal.my-water-bill') }}" class="print-btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Return to Portal
        </a>
        <button onclick="window.print()" class="print-btn">
            <i class="bi bi-printer"></i> Print Statement
        </button>
    </div>

    <div class="statement-card">
        <div class="header">
            <img src="{{ asset('photos/brgylogo.jpeg') }}" alt="Barangay Caruhayon Logo" class="header-logo" style="border-radius: 50%;">
            <div class="header-center">
                <h3>Republic of the Philippines &bull; Province of Sorsogon</h3>
                <h1>{{ $barangayName }}</h1>
                <p>Community Water Service System &bull; Municipality of Juban</p>
                <div class="statement-badge">
                    OFFICIAL WATER BILL STATEMENT
                </div>
            </div>
            <img src="{{ asset('photos/Aquatank logo.png') }}" alt="AquaTank Logo" class="header-logo">
        </div>

        <div class="meta-grid">
            <div class="meta-box">
                <div class="meta-title">Consumer Information</div>
                <div class="row">
                    <span>Account Name:</span>
                    <span>{{ $bill->consumer->full_name }}</span>
                </div>
                <div class="row">
                    <span>Account Number:</span>
                    <span style="color: #0284c7; font-family: monospace; font-size: 14px;">{{ $bill->consumer->account_number }}</span>
                </div>
                <div class="row">
                    <span>Meter Serial Number:</span>
                    <span style="font-family: monospace;">{{ $bill->consumer->meter_number }}</span>
                </div>
                <div class="row">
                    <span>Service Address:</span>
                    <span style="text-align: right; max-width: 65%;">{{ $bill->consumer->address }}</span>
                </div>
            </div>

            <div class="meta-box">
                <div class="meta-title">Statement Particulars</div>
                <div class="row">
                    <span>Statement Number:</span>
                    <span style="font-family: monospace; font-weight: bold;">{{ $bill->bill_number }}</span>
                </div>
                <div class="row">
                    <span>Billing Period:</span>
                    <span>{{ $bill->billing_period }}</span>
                </div>
                <div class="row">
                    <span>Reading Date:</span>
                    <span>{{ $bill->billing_date->format('F d, Y') }}</span>
                </div>
                <div class="row">
                    <span>Payment Due Date:</span>
                    <span style="color: #b91c1c; font-weight: bold;">{{ $bill->due_date->format('F d, Y') }}</span>
                </div>
                <div class="row">
                    <span>Payment Status:</span>
                    <span>
                        <span class="status-badge status-{{ $bill->status }}">{{ $bill->status }}</span>
                    </span>
                </div>
                @if($bill->payments->isNotEmpty())
                    @php $latestPayment = $bill->payments->last(); @endphp
                    <div class="row" style="margin-top: 8px; padding-top: 6px; border-top: 1px dashed #cbd5e1;">
                        <span style="color: #166534; font-weight: bold;">Payment Date:</span>
                        <span style="color: #166534;">{{ $latestPayment->payment_date->format('M d, Y') }} (OR #{{ $latestPayment->reference_number }})</span>
                    </div>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Previous Reading</th>
                    <th>Current Reading</th>
                    <th>Consumption ($m^3$)</th>
                    <th>Tariff Rate</th>
                    <th style="text-align: right;">Amount Due</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ number_format($bill->previous_reading, 2) }} m³</td>
                    <td>{{ number_format($bill->current_reading, 2) }} m³</td>
                    <td><strong>{{ number_format($bill->consumption, 2) }} m³</strong></td>
                    <td>₱{{ number_format($bill->rate_per_cubic_meter, 2) }} / m³</td>
                    <td style="text-align: right; font-size: 15px; font-weight: bold; color: #0284c7;">
                        ₱{{ number_format($bill->amount_due, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="total-box">
            <div class="label">Total Amount Due:</div>
            <div class="amount">₱{{ number_format($bill->amount_due, 2) }}</div>
            @if($bill->total_paid > 0)
                <div class="payment-summary">
                    <span style="color: #166534; font-weight: 600;">Total Paid: ₱{{ number_format($bill->total_paid, 2) }}</span>
                    &bull;
                    <span style="color: {{ $bill->balance > 0 ? '#b91c1c' : '#166534' }}; font-weight: bold;">
                        Remaining Balance: ₱{{ number_format($bill->balance, 2) }}
                    </span>
                </div>
            @endif
        </div>

        <div class="footer-note">
            <p><strong>Payment Instructions:</strong> All water payments must be remitted directly to the Barangay Treasurer at the Barangay Caruhayon Hall. Please present this Official Statement upon payment.</p>
            <p style="margin: 4px 0 0 0;">For billing inquiries or questions, contact: <strong>{{ $systemContact }}</strong> &bull; AquaTank 2.0</p>
        </div>
    </div>

</body>
</html>
