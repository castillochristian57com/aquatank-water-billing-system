<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meter Reading Receipt - {{ $reading->consumer->account_number }}</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Arial, Helvetica, sans-serif;
            color: #172033;
            background: #eef2f6;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 24px 16px;
            background: #eef2f6;
        }

        .receipt {
            width: min(100%, 820px);
            margin: 0 auto;
            padding: 18px;
            background: #fff;
        }

        .title {
            margin: 0 0 12px;
            padding: 8px 12px;
            border-radius: 5px;
            background: #125592;
            color: #fff;
            text-align: center;
            font-size: 24px;
            line-height: 1.2;
        }

        .organization {
            margin: 0 0 14px;
            color: #42526b;
            text-align: center;
            font-size: 12px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 10px;
            margin-bottom: 12px;
        }

        .details-panel { border: 1px solid #72849b; }

        .panel-title {
            margin: 0;
            padding: 5px 8px;
            background: #cfe2f7;
            color: #172033;
            font-size: 15px;
        }

        .detail-list { padding: 6px 8px; }

        .detail-row {
            display: grid;
            grid-template-columns: minmax(100px, 1fr) 12px minmax(0, 1.3fr);
            gap: 4px;
            padding: 2px 0;
            font-size: 13px;
            line-height: 1.35;
        }

        .detail-row strong { overflow-wrap: anywhere; }

        .section-title {
            margin: 12px 0 0;
            padding: 5px 9px;
            background: #125592;
            color: #fff;
            font-size: 15px;
        }

        .reading-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            border: 1px solid #72849b;
            text-align: center;
        }

        .reading-cell {
            padding: 7px 4px;
            border-right: 1px solid #72849b;
            font-size: 13px;
        }

        .reading-cell:last-child { border-right: 0; }
        .reading-value { display: block; margin-top: 3px; font-size: 16px; font-weight: 700; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th, td { padding: 5px 8px; border: 1px solid #72849b; }
        th { background: #cfe2f7; text-align: center; }
        td:last-child, th:last-child { text-align: right; }

        .total-row { background: #cfe2f7; color: #123864; font-size: 16px; font-weight: 700; }

        .reminders {
            width: min(100%, 520px);
            margin: 14px 0 0 8px;
            padding: 8px 10px;
            border: 1px solid #125592;
            border-radius: 6px;
            color: #173c70;
            font-size: 12px;
        }

        .reminders h2 { margin: 0 0 4px; font-size: 14px; }
        .reminders ol { margin: 0; padding-left: 18px; color: #172033; }
        .reminders li { padding: 1px 0; }

        .actions {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 0 auto 14px;
        }

        .actions a, .actions button {
            padding: 8px 12px;
            border: 0;
            border-radius: 4px;
            background: #125592;
            color: white;
            font: inherit;
            text-decoration: none;
            cursor: pointer;
        }

        .actions a { background: #526174; }

        @media (max-width: 560px) {
            body { padding: 10px 6px; }
            .receipt { padding: 8px; }
            .title { font-size: 20px; }
            .details-grid { grid-template-columns: 1fr; }
            .detail-row { grid-template-columns: minmax(95px, 1fr) 10px minmax(0, 1.2fr); font-size: 12px; }
            .reading-cell { font-size: 11px; }
            .reading-value { font-size: 14px; }
        }

        @media print {
            @page { size: auto; margin: 10mm; }
            :root, body { background: #fff; }
            body { padding: 0; }
            .receipt { width: 100%; padding: 0; }
            .no-print { display: none !important; }
            .title, .section-title, th, .total-row, .panel-title {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <nav class="actions no-print" aria-label="Receipt actions">
        <a href="{{ route('meter-readings.create') }}">Record Next Reading</a>
        <button type="button" onclick="window.print()">Print Receipt</button>
    </nav>

    <main class="receipt">
        <h1 class="title">WATER METER READING RECEIPT</h1>
        <p class="organization">{{ $barangayName }} &bull; Community Water Service</p>

        <div class="details-grid">
            <section class="details-panel" aria-labelledby="account-title">
                <h2 class="panel-title" id="account-title">Account Information</h2>
                <div class="detail-list">
                    <div class="detail-row"><strong>Account No.</strong><span>:</span><span>{{ $reading->consumer->account_number }}</span></div>
                    <div class="detail-row"><strong>Consumer Name</strong><span>:</span><span>{{ $reading->consumer->full_name }}</span></div>
                    <div class="detail-row"><strong>Address</strong><span>:</span><span>{{ $reading->consumer->address }}</span></div>
                    <div class="detail-row"><strong>Contact No.</strong><span>:</span><span>{{ $reading->consumer->contact_number }}</span></div>
                    <div class="detail-row"><strong>Meter No.</strong><span>:</span><span>{{ $reading->consumer->meter_number }}</span></div>
                </div>
            </section>

            <section class="details-panel" aria-labelledby="billing-title">
                <h2 class="panel-title" id="billing-title">Reading Information</h2>
                <div class="detail-list">
                    <div class="detail-row"><strong>Billing Period</strong><span>:</span><span>{{ $reading->billing_period }}</span></div>
                    @if($reading->bill)
                        <div class="detail-row"><strong>Bill No.</strong><span>:</span><span>{{ $reading->bill->bill_number }}</span></div>
                        <div class="detail-row"><strong>Date Issued</strong><span>:</span><span>{{ $reading->bill->billing_date->format('M d, Y') }}</span></div>
                        <div class="detail-row"><strong>Due Date</strong><span>:</span><span>{{ $reading->bill->due_date->format('M d, Y') }}</span></div>
                    @else
                        <div class="detail-row"><strong>Reading No.</strong><span>:</span><span>MR-{{ $reading->reading_date->format('Y') }}-{{ str_pad($reading->id, 6, '0', STR_PAD_LEFT) }}</span></div>
                        <div class="detail-row"><strong>Date Recorded</strong><span>:</span><span>{{ $reading->created_at->format('M d, Y') }}</span></div>
                        <div class="detail-row"><strong>Billing</strong><span>:</span><span>Bill issued separately</span></div>
                    @endif
                </div>
            </section>
        </div>

        <h2 class="section-title">Meter Reading Details</h2>
        <div class="reading-grid">
            <div class="reading-cell">Previous Reading<span class="reading-value">{{ number_format($reading->previous_reading, 2) }} m³</span></div>
            <div class="reading-cell">Current Reading<span class="reading-value">{{ number_format($reading->current_reading, 2) }} m³</span></div>
            <div class="reading-cell">Consumption<span class="reading-value">{{ number_format($reading->consumption, 2) }} m³</span></div>
        </div>

        <h2 class="section-title">Billing Breakdown</h2>
        <table>
            <thead>
                <tr><th>Description</th><th>Amount (PHP)</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>Water Consumption ({{ number_format($reading->consumption, 2) }} m³ @ ₱{{ number_format($tariffRate, 2) }}/m³)</td>
                    <td>₱{{ number_format($estimatedAmount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>{{ $reading->bill ? 'TOTAL AMOUNT DUE' : 'ESTIMATED WATER CHARGE' }}</td>
                    <td>₱{{ number_format($estimatedAmount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <aside class="reminders">
            <h2>Important Reminders:</h2>
            <ol>
                <li>This receipt confirms the meter reading; it is not proof of payment.</li>
                <li>Your official bill and due date will be issued separately.</li>
                <li>For inquiries, contact {{ $systemContact }}.</li>
            </ol>
        </aside>
    </main>

</body>
</html>