<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Water Bill | Consumer Portal - Barangay Caruhayon</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #155eef;
            --primary-dark: #1d4ed8;
            --cyan: #06b6d4;
            --dark-navy: #0f2b5b;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --font-heading: 'Poppins', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background: #f4faff;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* HEADER */
        .portal-header {
            background: white;
            padding: 16px 7%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 15px rgba(15, 43, 91, 0.05);
        }

        .portal-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #155eef, #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .logo-text {
            font-size: 20px;
            font-weight: 700;
            color: #155eef;
            line-height: 1.1;
        }

        .logo-sub {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            color: #475569;
            transition: all 0.2s;
        }

        .btn-outline:hover {
            border-color: #155eef;
            color: #155eef;
            background: #eff6ff;
        }

        /* MAIN WRAPPER */
        .portal-main {
            flex: 1;
            padding: 50px 7% 70px;
            max-width: 1200px;
            margin: auto;
            width: 100%;
        }

        /* SEARCH HERO CARD */
        .search-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(15, 43, 91, 0.06);
            border: 1.5px solid #e0f2fe;
            margin-bottom: 35px;
            text-align: center;
        }

        .portal-badge {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0284c7;
            background: #e0f2fe;
            padding: 5px 14px;
            border-radius: 20px;
            margin-bottom: 12px;
        }

        .search-card h2 {
            font-size: 30px;
            color: var(--dark-navy);
            margin-bottom: 10px;
        }

        .search-card p {
            color: var(--text-muted);
            font-size: 15px;
            max-width: 600px;
            margin: 0 auto 28px;
        }

        .search-form {
            display: flex;
            max-width: 640px;
            margin: auto;
            gap: 12px;
        }

        .search-input {
            flex: 1;
            padding: 14px 20px;
            border-radius: 12px;
            border: 1.5px solid #cbd5e1;
            font-size: 16px;
            font-weight: 600;
            outline: none;
            transition: all 0.2s;
        }

        .search-input:focus {
            border-color: #155eef;
            box-shadow: 0 0 0 4px rgba(21, 94, 239, 0.12);
        }

        .btn-search {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #0284c7 0%, #155eef 100%);
            color: white;
            padding: 14px 28px;
            border-radius: 12px;
            border: none;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-search:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(2, 132, 199, 0.35);
        }

        .demo-pills {
            margin-top: 18px;
            font-size: 13px;
            color: #64748b;
        }

        .demo-pills a {
            color: #155eef;
            font-weight: 600;
            background: #eff6ff;
            padding: 3px 10px;
            border-radius: 6px;
            margin: 0 4px;
            display: inline-block;
        }

        .demo-pills a:hover {
            background: #dbeafe;
        }

        /* ALERT */
        .alert-box {
            padding: 18px 24px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 14.5px;
            font-weight: 500;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* CONSUMER PROFILE & CURRENT BILL GRID */
        .bill-grid {
            display: grid;
            grid-template-columns: 1fr 1.6fr;
            gap: 25px;
            margin-bottom: 35px;
        }

        .card {
            background: white;
            border-radius: 18px;
            border: 1.5px solid #e0f2fe;
            box-shadow: 0 6px 20px rgba(15, 43, 91, 0.04);
            overflow: hidden;
        }

        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid #f1f5f9;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--dark-navy);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-body {
            padding: 24px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 14px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
            font-weight: 500;
        }

        .info-value {
            font-weight: 700;
            color: #0f172a;
            text-align: right;
        }

        /* BILL HIGHLIGHT BOX */
        .bill-highlight {
            background: linear-gradient(135deg, #0284c7 0%, #1d4ed8 100%);
            color: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .bill-amount-title {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #bfdbfe;
            font-weight: 700;
        }

        .bill-amount-val {
            font-size: 38px;
            font-weight: 800;
            line-height: 1.1;
        }

        .badge-status {
            display: inline-block;
            font-size: 12px;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-unpaid { background: #fee2e2; color: #b91c1c; }
        .status-paid { background: #dcfce7; color: #15803d; }
        .status-overdue { background: #ffedd5; color: #c2410c; }

        /* LEDGER TABLE */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11.5px;
            letter-spacing: 0.6px;
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1.5px solid #e2e8f0;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        tr:hover td {
            background: #f8fcff;
        }

        .btn-sm-action {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 12px;
            background: #eff6ff;
            color: #155eef;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #bfdbfe;
        }

        .btn-sm-action:hover {
            background: #155eef;
            color: white;
        }

        .otc-notice {
            background: #f0fdf4;
            border: 1.5px solid #bbf7d0;
            border-radius: 14px;
            padding: 18px 22px;
            margin-top: 25px;
            display: flex;
            align-items: center;
            gap: 14px;
            color: #166534;
            font-size: 13.5px;
        }

        /* FOOTER */
        footer {
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 24px 7%;
            text-align: center;
            font-size: 13px;
            color: #94a3b8;
        }

        @media (max-width: 900px) {
            .bill-grid {
                grid-template-columns: 1fr;
            }
            .search-form {
                flex-direction: column;
            }
            .btn-search {
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <header class="portal-header">
        <a href="/" class="portal-logo">
            <div class="logo-icon">💧</div>
            <div>
                <div class="logo-text">AquaTank</div>
                <div class="logo-sub">Barangay Caruhayon Consumer Portal</div>
            </div>
        </a>

        <div class="header-actions">
            <a href="/" class="btn-outline">
                <i class="bi bi-arrow-left"></i> Back to Home
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn-outline">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-outline">
                    <i class="bi bi-lock-fill"></i> Staff Login
                </a>
            @endauth
        </div>
    </header>

    <main class="portal-main">

        <div class="search-card">
            <span class="portal-badge">Official Consumer Portal</span>
            <h2>My Water Bill Lookup</h2>
            <p>
                Enter your assigned <strong>Account Number</strong> or <strong>Meter Number</strong> to review your monthly readings, consumption, payment status, and official statements.
            </p>

            <form method="GET" action="{{ route('portal.my-water-bill') }}" class="search-form">
                <input
                    type="text"
                    name="identifier"
                    class="search-input"
                    placeholder="Enter Account # (e.g. CRH-2026-0001) or Meter # (e.g. MTR-CAR-1001)"
                    value="{{ $identifier }}"
                    required
                    autofocus
                >
                <button type="submit" class="btn-search">
                    <i class="bi bi-search"></i> Look Up Bill
                </button>
            </form>

        </div>

        @if(session('error'))
            <div class="alert-box alert-danger">
                <i class="bi bi-shield-x fs-4"></i>
                <div>
                    <strong>{{ session('error') }}</strong>
                </div>
            </div>
        @endif

        @if($notFound)
            <div class="alert-box alert-danger">
                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                <div>
                    <strong>No consumer record found for "{{ $identifier }}".</strong><br>
                    Please verify your Account Number or Meter Number from your official receipt, or contact the Barangay Hall.
                </div>
            </div>
        @endif

        @if($consumer)
            <div class="bill-grid">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="bi bi-person-badge-fill text-primary"></i> Consumer Profile</h3>
                        <span class="badge-status {{ $consumer->status === 'active' ? 'status-paid' : 'status-unpaid' }}">
                            {{ strtoupper($consumer->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">Full Name</span>
                            <span class="info-value">{{ $consumer->full_name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Account Number</span>
                            <span class="info-value text-primary">{{ $consumer->account_number }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Meter Number</span>
                            <span class="info-value">{{ $consumer->meter_number }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Service Address</span>
                            <span class="info-value">{{ $consumer->address }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Registered Contact</span>
                            <span class="info-value">{{ $consumer->contact_number }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Connection Date</span>
                            <span class="info-value">{{ $consumer->connection_date ? $consumer->connection_date->format('M d, Y') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3><i class="bi bi-receipt text-primary"></i> Latest Billing Statement</h3>
                        @if($activeBill)
                            <span class="badge-status status-{{ $activeBill->status }}">
                                {{ strtoupper($activeBill->status) }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($activeBill)
                            <div class="bill-highlight">
                                <div>
                                    <div class="bill-amount-title">Total Amount Due</div>
                                    <div class="bill-amount-val">₱{{ number_format($activeBill->amount_due, 2) }}</div>
                                    <small style="opacity: 0.9;">Period: {{ $activeBill->billing_period }}</small>
                                </div>
                                <div class="text-end">
                                    <div style="font-size: 13px; color: #bfdbfe;">Due Date</div>
                                    <div style="font-size: 18px; font-weight: 700;">
                                        {{ $activeBill->due_date ? $activeBill->due_date->format('F d, Y') : 'N/A' }}
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ route('portal.statement', ['bill' => $activeBill, 'account' => $consumer->account_number]) }}" target="_blank" class="btn-outline" style="background: white; border: none; color: #1d4ed8; font-size: 12px; padding: 6px 14px;">
                                            <i class="bi bi-printer"></i> Print Statement
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Bill Number</span>
                                <span class="info-value font-monospace">{{ $activeBill->bill_number }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Previous Reading</span>
                                <span class="info-value">{{ number_format($activeBill->previous_reading, 2) }} m³</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Current Reading</span>
                                <span class="info-value">{{ number_format($activeBill->current_reading, 2) }} m³</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Total Water Consumed</span>
                                <span class="info-value text-primary">{{ number_format($activeBill->consumption, 2) }} m³</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Standard Barangay Rate</span>
                                <span class="info-value">₱{{ number_format($activeBill->rate_per_cubic_meter, 2) }} / m³</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Remaining Balance</span>
                                <span class="info-value {{ $activeBill->balance > 0 ? 'text-danger' : 'text-success' }}">
                                    ₱{{ number_format($activeBill->balance, 2) }}
                                </span>
                            </div>
                        @else
                            <p class="text-muted text-center py-4">No billing records found for this account yet.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h3><i class="bi bi-clock-history text-primary"></i> Billing & Payment History</h3>
                    <span class="small text-muted">Showing all recorded transactions</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Bill #</th>
                                    <th>Period</th>
                                    <th>Previous</th>
                                    <th>Current</th>
                                    <th>Consumption</th>
                                    <th>Amount Due</th>
                                    <th>Status</th>
                                    <th>Payment Records</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bills as $bill)
                                    <tr>
                                        <td class="font-monospace fw-bold">{{ $bill->bill_number }}</td>
                                        <td>{{ $bill->billing_period }}</td>
                                        <td>{{ number_format($bill->previous_reading, 2) }} m³</td>
                                        <td>{{ number_format($bill->current_reading, 2) }} m³</td>
                                        <td class="fw-bold text-primary">{{ number_format($bill->consumption, 2) }} m³</td>
                                        <td class="fw-bold">₱{{ number_format($bill->amount_due, 2) }}</td>
                                        <td>
                                            <span class="badge-status status-{{ $bill->status }}">
                                                {{ strtoupper($bill->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($bill->payments->count() > 0)
                                                @foreach($bill->payments as $pmt)
                                                    <span class="badge bg-success-subtle text-success small d-inline-block me-1">
                                                        OR #{{ $pmt->reference_number }} (₱{{ number_format($pmt->amount_paid, 2) }})
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted small">No payment recorded</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('portal.statement', ['bill' => $bill, 'account' => $consumer->account_number]) }}" target="_blank" class="btn-sm-action">
                                                <i class="bi bi-file-text"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">No historical statements recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="otc-notice">
                <i class="bi bi-info-circle-fill fs-3"></i>
                <div>
                    <strong>Over-The-Counter Payment Notice:</strong>
                    Water bills are settled in cash at the Barangay Treasury Office in Barangay Caruhayon Hall (Monday to Friday, 8:00 AM – 5:00 PM). Please present your Account Number or Statement upon payment.
                </div>
            </div>
        @endif

    </main>

    <footer>
        <p>&copy; {{ date('Y') }} AquaTank Water Billing System &bull; Barangay Caruhayon, Juban, Sorsogon</p>
    </footer>

</body>
</html>
