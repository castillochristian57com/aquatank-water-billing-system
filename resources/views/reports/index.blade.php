@extends('adminlte::page')

@section('title', 'Operational Reports')

@push('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --font-heading: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --glass-bg: #ffffff;
    --glass-border: #bfdbfe;
    --glass-shadow: 0 4px 16px rgba(37, 99, 235, 0.06);
}
body, .table, .form-control, .form-select, .btn, .badge {
    font-family: var(--font-body) !important;
}
h1, h2, h3, h4, h5, h6, .card-title, .stat-number {
    font-family: var(--font-heading) !important;
    color: #0f172a !important;
}
.stat-card {
    background: #ffffff !important;
    border: 1.5px solid #dbeafe !important;
    border-radius: 1rem !important;
    padding: 1.25rem 1.5rem !important;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.05) !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(37, 99, 235, 0.12) !important;
}
.nav-pills .nav-link {
    border-radius: 20px !important;
    font-weight: 600 !important;
    font-size: 0.875rem !important;
    padding: 8px 18px !important;
    color: #1e293b !important;
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
}
.nav-pills .nav-link:hover {
    color: #2563eb !important;
    background: #eff6ff !important;
}
.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
    color: #ffffff !important;
    border-color: transparent !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;
}
.table thead th {
    background: #eef6ff !important;
    color: #1e3a8a !important;
    font-weight: 700 !important;
    font-size: 0.78rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    border-bottom: 2px solid #bfdbfe !important;
    padding: 0.85rem 1rem !important;
}
.table tbody td {
    background: #ffffff !important;
    color: #0f172a !important;
    padding: 0.85rem 1rem !important;
    border-bottom: 1px solid #e2e8f0 !important;
    vertical-align: middle !important;
}
.table-hover tbody tr:hover td {
    background-color: #f0f7ff !important;
}
</style>
@endpush

@section('content_header')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">
            <i class="bi bi-file-earmark-bar-graph-fill text-primary me-2"></i>Operational Reports & Analytics
        </h1>
        <p class="text-muted small mb-0 fw-semibold">Auditing, financial collections, water consumption statistics, and delinquency aging</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.print', request()->query()) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm fw-semibold">
            <i class="bi bi-printer-fill me-1"></i> Print Official Report
        </a>
        <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-sm fw-semibold">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Export to CSV
        </a>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-0">

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <ul class="nav nav-pills gap-2 flex-wrap">
                <li class="nav-item">
                    <a class="nav-link {{ $reportType === 'billing_collection' ? 'active' : '' }}" href="{{ route('reports.index', ['report_type' => 'billing_collection', 'date_from' => $dateFrom, 'date_to' => $dateTo, 'purok' => $purokFilter]) }}">
                        <i class="bi bi-cash-coin me-1"></i> Billing & Collection Summary
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $reportType === 'consumption' ? 'active' : '' }}" href="{{ route('reports.index', ['report_type' => 'consumption', 'date_from' => $dateFrom, 'date_to' => $dateTo, 'purok' => $purokFilter]) }}">
                        <i class="bi bi-droplet-half me-1"></i> Water Consumption & Metering
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $reportType === 'delinquency' ? 'active' : '' }}" href="{{ route('reports.index', ['report_type' => 'delinquency', 'date_from' => $dateFrom, 'date_to' => $dateTo, 'purok' => $purokFilter]) }}">
                        <i class="bi bi-exclamation-octagon me-1"></i> Delinquent & Overdue Accounts
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $reportType === 'cashier' ? 'active' : '' }}" href="{{ route('reports.index', ['report_type' => 'cashier', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
                        <i class="bi bi-receipt me-1"></i> Cashier Collection Audit Log
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="report_type" value="{{ $reportType }}">

                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold text-dark mb-1">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm rounded-3" value="{{ $dateFrom }}">
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold text-dark mb-1">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm rounded-3" value="{{ $dateTo }}">
                </div>

                @if($reportType !== 'cashier')
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold text-dark mb-1">Purok / Zone</label>
                    <select name="purok" class="form-select form-select-sm rounded-3">
                        <option value="">All Puroks</option>
                        @foreach($purokList as $p)
                            <option value="{{ $p }}" {{ $purokFilter === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-3 col-sm-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 mt-4">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                    <a href="{{ route('reports.index', ['report_type' => $reportType]) }}" class="btn btn-light btn-sm rounded-pill px-3 mt-4 border">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($reportType === 'billing_collection')
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card" style="border-top: 3.5px solid #2563eb !important;">
                <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #1e3a8a;">Total Billed</span>
                <h3 class="stat-number fw-bold text-primary mb-0 mt-1">₱{{ number_format($totalBilled, 2) }}</h3>
                <small class="text-muted fw-semibold">{{ count($bills) }} billing records generated</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card" style="border-top: 3.5px solid #16a34a !important;">
                <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #15803d;">Total Collected</span>
                <h3 class="stat-number fw-bold text-success mb-0 mt-1">₱{{ number_format($totalCollected, 2) }}</h3>
                <small class="text-muted fw-semibold">Settled via official cashier</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card" style="border-top: 3.5px solid #dc2626 !important;">
                <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #b91c1c;">Outstanding Arrears</span>
                <h3 class="stat-number fw-bold text-danger mb-0 mt-1">₱{{ number_format($totalArrears, 2) }}</h3>
                <small class="text-muted fw-semibold">Uncollected receivables</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card" style="border-top: 3.5px solid #0891b2 !important;">
                <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #0e7490;">Collection Efficiency</span>
                <h3 class="stat-number fw-bold text-dark mb-0 mt-1">{{ number_format($collectionEfficiency, 1) }}%</h3>
                <div class="progress mt-2" style="height: 7px; background-color: #e2e8f0;">
                    <div class="progress-bar bg-success rounded" style="width: {{ min(100, $collectionEfficiency) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3 border-light">
            <h5 class="card-title fw-bold text-dark mb-0">Detailed Billing & Collection Audit Log</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Bill No</th>
                            <th>Consumer</th>
                            <th>Purok</th>
                            <th>Period</th>
                            <th>Consumption</th>
                            <th>Amount Due</th>
                            <th>Total Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bills as $b)
                        <tr>
                            <td class="ps-4 font-monospace fw-bold text-primary">{{ $b->bill_number }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $b->consumer->full_name }}</div>
                                <small class="text-muted fw-semibold">{{ $b->consumer->account_number }}</small>
                            </td>
                            <td>{{ $b->consumer->purok }}</td>
                            <td>{{ $b->billing_period }}</td>
                            <td>{{ number_format($b->consumption, 1) }} m³</td>
                            <td class="fw-bold">₱{{ number_format($b->amount_due, 2) }}</td>
                            <td class="text-success fw-bold">₱{{ number_format($b->total_paid, 2) }}</td>
                            <td class="text-danger fw-bold">₱{{ number_format($b->balance, 2) }}</td>
                            <td>
                                <span class="badge {{ $b->status === 'paid' ? 'bg-success' : ($b->status === 'overdue' ? 'bg-danger' : 'bg-warning text-dark') }} rounded-pill px-2 py-1">
                                    {{ strtoupper($b->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No billing transactions matching the current filters.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($reportType === 'consumption')
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="stat-card" style="border-top: 3.5px solid #0284c7 !important;">
                <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #0369a1;">Total Water Consumed</span>
                <h3 class="stat-number fw-bold text-info mb-0 mt-1">{{ number_format($totalVolume, 2) }} m³</h3>
                <small class="text-muted fw-semibold">Total metered distribution</small>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="stat-card" style="border-top: 3.5px solid #2563eb !important;">
                <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #1e3a8a;">Average per Household</span>
                <h3 class="stat-number fw-bold text-primary mb-0 mt-1">{{ number_format($avgHouseholdVolume, 2) }} m³</h3>
                <small class="text-muted fw-semibold">Per active connected household</small>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="stat-card" style="border-top: 3.5px solid #16a34a !important;">
                <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #15803d;">Water Rate</span>
                <h3 class="stat-number fw-bold text-success mb-0 mt-1">₱{{ number_format($waterRate, 2) }} / m³</h3>
                <small class="text-muted fw-semibold">Barangay standardized rate</small>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-light">
                    <h5 class="card-title fw-bold text-dark mb-0">Consumption Distribution by Purok</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Purok / Zone</th>
                                <th>Consumers</th>
                                <th>Total Consumed (m³)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($consumptionByPurok as $cp)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $cp->purok }}</td>
                                <td>{{ $cp->consumers_count }} connections</td>
                                <td class="fw-bold text-primary">{{ number_format($cp->total_cbm, 1) }} m³</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-light">
                    <h5 class="card-title fw-bold text-dark mb-0">Top Consuming Households</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Consumer</th>
                                <th>Meter No</th>
                                <th>Consumption</th>
                                <th>Billed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topConsumers as $tc)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold text-dark">{{ $tc->full_name }}</div>
                                    <small class="text-muted fw-semibold">{{ $tc->purok }}</small>
                                </td>
                                <td class="font-monospace small">{{ $tc->meter_number }}</td>
                                <td class="fw-bold text-info">{{ number_format($tc->bills_sum_consumption, 1) }} m³</td>
                                <td class="fw-bold text-dark">₱{{ number_format($tc->bills_sum_amount_due, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No consumption records available.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($reportType === 'delinquency')
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card" style="border-top: 3.5px solid #dc2626 !important;">
                <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #b91c1c;">Total Delinquent Accounts</span>
                <h3 class="stat-number fw-bold text-danger mb-0 mt-1">{{ count($delinquentBills) }} Bills</h3>
                <small class="text-muted fw-semibold">Unpaid accounts exceeding the due date</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card" style="border-top: 3.5px solid #dc2626 !important;">
                <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #b91c1c;">Total Overdue Receivables</span>
                <h3 class="stat-number fw-bold text-danger mb-0 mt-1">₱{{ number_format($totalOverdueAmount, 2) }}</h3>
                <small class="text-muted fw-semibold">Urgent collection balance</small>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3 border-light d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold text-dark mb-0">Delinquent Water Consumers Aging Schedule</h5>
            <form action="{{ route('sms.batch-overdue') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm fw-semibold" onclick="return confirm('Send automated overdue SMS alert to all delinquent consumers?')">
                    <i class="bi bi-broadcast me-1"></i> Send Overdue SMS to All
                </button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Bill No</th>
                            <th>Consumer</th>
                            <th>Purok</th>
                            <th>Contact</th>
                            <th>Due Date</th>
                            <th>Days Overdue</th>
                            <th>Outstanding</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($delinquentBills as $db)
                        @php
                            $daysOverdue = max(0, \Carbon\Carbon::parse($db->due_date)->diffInDays(\Carbon\Carbon::today(), false));
                        @endphp
                        <tr>
                            <td class="ps-4 font-monospace fw-bold text-primary">{{ $db->bill_number }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $db->consumer->full_name }}</div>
                                <small class="text-muted fw-semibold">{{ $db->consumer->account_number }}</small>
                            </td>
                            <td>{{ $db->consumer->purok }}</td>
                            <td>{{ $db->consumer->contact_number ?: 'N/A' }}</td>
                            <td class="text-danger fw-semibold">{{ \Carbon\Carbon::parse($db->due_date)->format('M d, Y') }}</td>
                            <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">{{ $daysOverdue }} days</span></td>
                            <td class="fw-bold text-danger">₱{{ number_format($db->balance, 2) }}</td>
                            <td class="text-end pe-4">
                                <form action="{{ route('sms.send-overdue-alert', $db) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0 fw-semibold" title="Dispatch SMS Alert">
                                        <i class="bi bi-chat-dots-fill"></i> Alert
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-2"></i>
                                Excellent! No delinquent water bills recorded.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($reportType === 'cashier')
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="stat-card" style="border-top: 3.5px solid #16a34a !important;">
                <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #15803d;">Total Cash Collections</span>
                <h3 class="stat-number fw-bold text-success mb-0 mt-1">₱{{ number_format($cashTotal, 2) }}</h3>
                <small class="text-muted fw-semibold">Settled via Over-The-Counter Cash</small>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="stat-card" style="border-top: 3.5px solid #2563eb !important;">
                <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #1e3a8a;">Other Collections</span>
                <h3 class="stat-number fw-bold text-primary mb-0 mt-1">₱{{ number_format($otherTotal, 2) }}</h3>
                <small class="text-muted fw-semibold">Other payment channels</small>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="stat-card" style="border-top: 3.5px solid #0f172a !important;">
                <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #0f172a;">Grand Total Collected</span>
                <h3 class="stat-number fw-bold text-dark mb-0 mt-1">₱{{ number_format($grandPaymentsTotal, 2) }}</h3>
                <small class="text-muted fw-semibold">{{ count($payments) }} Official Receipts</small>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3 border-light">
            <h5 class="card-title fw-bold text-dark mb-0">Official Receipt Transactions Audit Trail</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">OR Reference No</th>
                            <th>Payment Date</th>
                            <th>Consumer Name</th>
                            <th>Bill Number</th>
                            <th>Amount Paid</th>
                            <th>Method</th>
                            <th class="text-end pe-4">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $p)
                        <tr>
                            <td class="ps-4 font-monospace fw-bold text-success">{{ $p->reference_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $p->bill?->consumer?->full_name }}</div>
                                <small class="text-muted fw-semibold">{{ $p->bill?->consumer?->account_number }}</small>
                            </td>
                            <td class="font-monospace small text-primary">{{ $p->bill?->bill_number }}</td>
                            <td class="fw-bold text-success">₱{{ number_format($p->amount_paid, 2) }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $p->payment_method }}</span></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('payments.receipt', $p) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-2" title="Print OR">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No cashier collection transactions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
@stop
