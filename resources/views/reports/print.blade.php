<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Operational Report - {{ strtoupper(str_replace('_', ' ', $reportType)) }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #ffffff;
            color: #0f172a;
            padding: 30px;
            margin: 0;
            font-size: 13px;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
            position: relative;
        }
        .header img {
            position: absolute;
            top: 0;
            height: 65px;
            width: 65px;
            object-fit: cover;
            border-radius: 50%;
        }
        .header .logo-left { left: 15px; }
        .header .logo-right { right: 15px; border-radius: 8px; width: auto; }
        .header h4 {
            margin: 0;
            font-size: 13px;
            font-weight: normal;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 3px 0;
            font-size: 15px;
            font-weight: 700;
        }
        .header h2 {
            margin: 8px 0 2px;
            font-size: 18px;
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .meta-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 12px;
            color: #475569;
            background: #f8fafc;
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            font-weight: 700;
            font-size: 11.5px;
            text-transform: uppercase;
            color: #334155;
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .total-row {
            background-color: #f8fafc;
            font-weight: bold;
            border-top: 2px solid #0f172a;
        }
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .sign-box {
            width: 28%;
            text-align: center;
        }
        .sign-line {
            border-bottom: 1px solid #0f172a;
            margin-top: 40px;
            margin-bottom: 6px;
        }
        .no-print-bar {
            background: #2563eb;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: white;
            color: #2563eb;
            border: none;
            padding: 8px 20px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        @media print {
            .no-print-bar { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="no-print-bar">
        <span><strong>Official Document:</strong> Ready for physical or PDF printing</span>
        <button class="btn-print" onclick="window.print()">Print Document</button>
    </div>

    <div class="header">
        <img src="{{ asset('photos/brgylogo.jpeg') }}" class="logo-left" alt="Brgy Logo">
        <img src="{{ asset('photos/Aquatank logo.png') }}" class="logo-right" alt="AquaTank Logo">
        <h4>Republic of the Philippines</h4>
        <h3>Barangay Caruhayon Water Works & Sanitation System</h3>
        <h4>AquaTank Centralized Billing & Management Facility</h4>
        <h2>
            @if($reportType === 'billing_collection')
                Monthly Billing & Collection Summary Report
            @elseif($reportType === 'delinquency')
                Delinquent & Overdue Accounts Aging Report
            @elseif($reportType === 'cashier')
                Official Cashier Collection Audit Log
            @else
                Water Consumption & Metering Audit Report
            @endif
        </h2>
    </div>

    <div class="meta-bar">
        <div><strong>Reporting Period:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} &mdash; {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</div>
        <div><strong>Purok Filter:</strong> {{ $purokFilter ?: 'All Puroks' }}</div>
        <div><strong>Generated On:</strong> {{ date('F d, Y h:i A') }}</div>
    </div>

    @if($reportType === 'billing_collection')
        <table>
            <thead>
                <tr>
                    <th>Bill Number</th>
                    <th>Consumer Name</th>
                    <th>Purok</th>
                    <th>Period</th>
                    <th class="text-end">Consumption (m³)</th>
                    <th class="text-end">Amount Due</th>
                    <th class="text-end">Total Paid</th>
                    <th class="text-end">Balance</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @php $sumDue = 0; $sumPaid = 0; $sumBal = 0; $sumCbm = 0; @endphp
                @foreach($bills as $b)
                @php
                    $sumDue += $b->amount_due;
                    $sumPaid += $b->total_paid;
                    $sumBal += $b->balance;
                    $sumCbm += $b->consumption;
                @endphp
                <tr>
                    <td>{{ $b->bill_number }}</td>
                    <td>{{ $b->consumer->full_name }}</td>
                    <td>{{ $b->consumer->purok }}</td>
                    <td>{{ $b->billing_period }}</td>
                    <td class="text-end">{{ number_format($b->consumption, 1) }}</td>
                    <td class="text-end">₱{{ number_format($b->amount_due, 2) }}</td>
                    <td class="text-end">₱{{ number_format($b->total_paid, 2) }}</td>
                    <td class="text-end">₱{{ number_format($b->balance, 2) }}</td>
                    <td class="text-center">{{ strtoupper($b->status) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4">TOTALS ({{ count($bills) }} Records)</td>
                    <td class="text-end">{{ number_format($sumCbm, 1) }} m³</td>
                    <td class="text-end">₱{{ number_format($sumDue, 2) }}</td>
                    <td class="text-end">₱{{ number_format($sumPaid, 2) }}</td>
                    <td class="text-end">₱{{ number_format($sumBal, 2) }}</td>
                    <td class="text-center">{{ $sumDue > 0 ? number_format(($sumPaid / $sumDue) * 100, 1) : 0 }}% Eff.</td>
                </tr>
            </tbody>
        </table>
    @elseif($reportType === 'delinquency')
        <table>
            <thead>
                <tr>
                    <th>Bill Number</th>
                    <th>Account No</th>
                    <th>Consumer Name</th>
                    <th>Purok</th>
                    <th>Contact No</th>
                    <th>Due Date</th>
                    <th class="text-end">Outstanding Balance</th>
                </tr>
            </thead>
            <tbody>
                @php $totalOverdue = 0; @endphp
                @foreach($delinquentBills as $db)
                @php $totalOverdue += $db->balance; @endphp
                <tr>
                    <td>{{ $db->bill_number }}</td>
                    <td>{{ $db->consumer->account_number }}</td>
                    <td>{{ $db->consumer->full_name }}</td>
                    <td>{{ $db->consumer->purok }}</td>
                    <td>{{ $db->consumer->contact_number ?: 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($db->due_date)->format('M d, Y') }}</td>
                    <td class="text-end fw-bold">₱{{ number_format($db->balance, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="6">TOTAL OVERDUE RECEIVABLES</td>
                    <td class="text-end">₱{{ number_format($totalOverdue, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @elseif($reportType === 'cashier')
        <table>
            <thead>
                <tr>
                    <th>OR Reference No</th>
                    <th>Payment Date</th>
                    <th>Consumer Name</th>
                    <th>Bill Number</th>
                    <th>Payment Method</th>
                    <th class="text-end">Amount Paid</th>
                </tr>
            </thead>
            <tbody>
                @php $sumCol = 0; @endphp
                @foreach($payments as $p)
                @php $sumCol += $p->amount_paid; @endphp
                <tr>
                    <td>{{ $p->reference_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</td>
                    <td>{{ $p->bill?->consumer?->full_name }}</td>
                    <td>{{ $p->bill?->bill_number }}</td>
                    <td>{{ $p->payment_method }}</td>
                    <td class="text-end fw-bold">₱{{ number_format($p->amount_paid, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5">TOTAL CASHIER COLLECTIONS ({{ count($payments) }} ORs)</td>
                    <td class="text-end">₱{{ number_format($sumCol, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th>Purok / Zone</th>
                    <th class="text-center">Active Household Connections</th>
                    <th class="text-end">Total Consumption (m³)</th>
                    <th class="text-end">Estimated Billed Value</th>
                </tr>
            </thead>
            <tbody>
                @php $totCbm = 0; $totConn = 0; @endphp
                @foreach($consumptionByPurok as $cp)
                @php 
                    $totCbm += $cp->total_cbm; 
                    $totConn += $cp->consumers_count; 
                @endphp
                <tr>
                    <td>{{ $cp->purok }}</td>
                    <td class="text-center">{{ $cp->consumers_count }}</td>
                    <td class="text-end">{{ number_format($cp->total_cbm, 1) }} m³</td>
                    <td class="text-end">₱{{ number_format($cp->total_cbm * $waterRate, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td>TOTALS</td>
                    <td class="text-center">{{ $totConn }}</td>
                    <td class="text-end">{{ number_format($totCbm, 1) }} m³</td>
                    <td class="text-end">₱{{ number_format($totCbm * $waterRate, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="signatures">
        <div class="sign-box">
            <div class="sign-line"></div>
            <strong>{{ $preparedBy }}</strong><br>
            <span style="font-size: 11px; color: #64748b;">Prepared by (System Admin)</span>
        </div>
        <div class="sign-box">
            <div class="sign-line"></div>
            <strong>Barangay Treasurer</strong><br>
            <span style="font-size: 11px; color: #64748b;">Certified Correct</span>
        </div>
        <div class="sign-box">
            <div class="sign-line"></div>
            <strong>Punong Barangay</strong><br>
            <span style="font-size: 11px; color: #64748b;">Noted & Approved</span>
        </div>
    </div>
</div>

</body>
</html>
