@extends('adminlte::page')

@section('title', 'Admin Dashboard')

@push('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --font-heading: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --aquatank-blue: #4a6cf7;
    --aquatank-blue-dark: #2563eb;
    --aquatank-blue-light: #eff6ff;
    --aquatank-dark: #0f172a;
    --aquatank-text-slate: #334155;
    --aquatank-border-blue: #bfdbfe;

    --gradient-primary: linear-gradient(135deg, #4a6cf7 0%, #2563eb 100%);
    --gradient-sidebar: linear-gradient(180deg, #eef5fe 0%, #e2effe 50%, #d8eaff 100%);
    --glass-bg: #ffffff;
    --glass-border: #bfdbfe;
    --glass-shadow: 0 4px 16px rgba(37, 99, 235, 0.05);
    --glass-shadow-hover: 0 12px 28px -5px rgba(37, 99, 235, 0.16);
}

body, .app-wrapper, .content-wrapper, .app-main, p, span:not(.brand-text), table, .table, input, select, textarea, .form-control, .form-select, .btn, .badge, .dropdown-menu, .nav-link, .card-body {
    font-family: var(--font-body) !important;
}

h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, .card-title, .modal-title, .brand-text, .content-header h1, .stat-heading, .stat-number, .revenue-amount, .table thead th, .nav-header {
    font-family: var(--font-heading) !important;
    letter-spacing: -0.02em;
    color: #0f172a !important;
}

.app-wrapper, .app-main, .content-wrapper {
    background-color: #f4f8fe !important;
    background: linear-gradient(180deg, #f0f7ff 0%, #e8f2fe 35%, #ffffff 100%) !important;
    background-attachment: fixed !important;
    min-height: 100vh;
}

.text-muted {
    color: var(--aquatank-text-slate) !important;
}

.card, .glass-card {
    background: var(--glass-bg) !important;
    border: 1.5px solid var(--glass-border) !important;
    box-shadow: var(--glass-shadow) !important;
    border-radius: 1.15rem !important;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}

.stat-glass-card {
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease;
}

/* clickable interactive cards */
.clickable-card {
    cursor: pointer;
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease, border-color 0.25s ease !important;
}
.clickable-card:hover {
    transform: translateY(-5px) !important;
    box-shadow: var(--glass-shadow-hover) !important;
    border-color: #2563eb !important;
}
.clickable-card:hover .revenue-amount,
.clickable-card:hover .stat-number {
    transform: scale(1.02);
}

.app-header {
    background: #ffffff !important;
    border-bottom: 1.5px solid var(--aquatank-border-blue) !important;
    box-shadow: 0 2px 10px rgba(37, 99, 235, 0.04) !important;
}

/* sidebar light gradient blue */
.app-sidebar {
    background: var(--gradient-sidebar) !important;
    border-right: 1.5px solid var(--aquatank-border-blue) !important;
    box-shadow: 4px 0 20px rgba(37, 99, 235, 0.04) !important;
}

.app-sidebar .sidebar-brand {
    background: rgba(255, 255, 255, 0.9) !important;
    border-bottom: 1.5px solid var(--aquatank-border-blue) !important;
    padding: 0.85rem 1rem;
}

.app-sidebar .brand-link {
    color: #0f172a !important;
    font-weight: 800 !important;
}

.app-sidebar .nav-link {
    color: #0f172a !important;
    font-weight: 600 !important;
    font-size: 0.92rem !important;
    padding: 0.65rem 0.95rem !important;
    margin: 3px 8px !important;
    border-radius: 0.75rem !important;
    transition: all 0.2s ease !important;
}

.app-sidebar .nav-link i {
    color: var(--aquatank-blue-dark) !important;
    font-size: 1.15rem !important;
}

.app-sidebar .nav-link:hover:not(.active) {
    background: rgba(255, 255, 255, 0.9) !important;
    color: #1d4ed8 !important;
    box-shadow: 0 2px 10px rgba(37, 99, 235, 0.08) !important;
    transform: translateX(3px);
}

.app-sidebar .nav-link.active {
    background: var(--gradient-primary) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35) !important;
}

.app-sidebar .nav-link.active i {
    color: #ffffff !important;
}

.app-sidebar .nav-header {
    color: #1e3a8a !important;
    font-weight: 800 !important;
    font-size: 0.72rem !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
    padding: 1rem 1.2rem 0.35rem !important;
}

/* table styling */
.table thead th {
    background: #eef6ff !important;
    color: #1e3a8a !important;
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 2px solid var(--aquatank-border-blue) !important;
    padding: 0.85rem 1rem !important;
}

.table tbody td {
    background: #ffffff !important;
    color: #0f172a !important;
    padding: 0.85rem 1rem !important;
    border-bottom: 1px solid #e2e8f0 !important;
    vertical-align: middle;
}

.table-hover tbody tr:hover td {
    background-color: #f0f7ff !important;
}

.btn-primary {
    background: var(--gradient-primary) !important;
    border: none !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25) !important;
    font-weight: 600;
}

.btn-glass-action {
    background: #ffffff;
    border: 1.5px solid var(--aquatank-border-blue);
    color: #1e3a8a;
    transition: all 0.2s ease;
    font-weight: 600;
}
.btn-glass-action:hover {
    background: var(--gradient-primary);
    border-color: transparent;
    color: #ffffff !important;
}
.btn-glass-action:hover i {
    color: #ffffff !important;
}

.badge-glass-white {
    background: #ffffff !important;
    color: #1d4ed8 !important;
    border: 1px solid var(--aquatank-border-blue) !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.06);
}
</style>
@endpush

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-2">
        <div>
            <h1 class="m-0 text-dark fw-bold" style="font-size: 1.85rem; font-family: var(--font-heading) !important;">
                <i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard Overview
            </h1>
            <p class="text-muted small mb-0 fw-semibold">
                Live water billing operations and consumer database for <strong>{{ $barangayName }}</strong>
            </p>
        </div>
        <div class="mt-3 mt-md-0 d-flex flex-wrap gap-2 align-items-center">
            <span class="badge badge-glass-white px-3 py-2 d-flex align-items-center rounded-3">
                <i class="bi bi-droplet-fill me-1 text-primary"></i> Tariff Rate: <strong class="ms-1">₱{{ number_format($tariffRate, 2) }} / m³</strong>
            </span>

            <a href="{{ route('meter-readings.create') }}" class="btn btn-primary btn-sm px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-1" title="Mobile-friendly field meter reading">
                <i class="bi bi-speedometer2"></i> Record Meter Reading
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid px-0">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert" style="background: rgba(209, 250, 229, 0.95); border: 1px solid rgba(16, 185, 129, 0.3);">
            <i class="bi bi-check-circle-fill me-2 text-success"></i><strong class="text-success">{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('consumers.index') }}" class="text-decoration-none d-block h-100">
                <div class="card glass-card stat-glass-card clickable-card h-100" style="border-top: 3.5px solid #2563eb !important;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.04em;">Total Consumers</span>
                            <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); width: 42px; height: 42px; border: 1px solid rgba(37, 99, 235, 0.2);">
                                <i class="bi bi-people-fill text-primary fs-5"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline mb-1">
                            <h2 class="fw-bold mb-0 text-dark stat-number">{{ $totalConsumers }}</h2>
                        </div>
                        <div class="small text-muted pt-2 border-top border-light d-flex justify-content-between align-items-center">
                            <span><strong class="text-success">{{ $activeConsumers }}</strong> Active &bull; {{ $inactiveConsumers }} Inactive</span>
                            <span class="text-primary fw-semibold"><i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('bills.index') }}" class="text-decoration-none d-block h-100">
                <div class="card glass-card stat-glass-card clickable-card h-100" style="border-top: 3.5px solid #0284c7 !important;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.04em;">Total Bills</span>
                            <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); width: 42px; height: 42px; border: 1px solid rgba(2, 132, 199, 0.25);">
                                <i class="bi bi-receipt text-primary fs-5"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline mb-1">
                            <h2 class="fw-bold mb-0 text-dark stat-number">{{ $totalBills }}</h2>
                        </div>
                        <div class="small text-muted pt-2 border-top border-light d-flex justify-content-between align-items-center">
                            <span>Monthly billing statements</span>
                            <span class="text-primary fw-semibold"><i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('bills.index', ['status' => 'paid']) }}" class="text-decoration-none d-block h-100">
                <div class="card glass-card stat-glass-card clickable-card h-100" style="border-top: 3.5px solid #16a34a !important;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.04em;">Paid Bills</span>
                            <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); width: 42px; height: 42px; border: 1px solid rgba(22, 163, 74, 0.25);">
                                <i class="bi bi-check2-all text-success fs-5"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline mb-1">
                            <h2 class="fw-bold mb-0 text-success stat-number">{{ $paidBills }}</h2>
                        </div>
                        <div class="small text-muted pt-2 border-top border-light d-flex justify-content-between align-items-center">
                            <span>Settled statements</span>
                            <span class="text-success fw-semibold"><i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('bills.index', ['status' => 'unpaid']) }}" class="text-decoration-none d-block h-100">
                <div class="card glass-card stat-glass-card clickable-card h-100" style="border-top: 3.5px solid #dc2626 !important;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.04em;">Unpaid Bills</span>
                            <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); width: 42px; height: 42px; border: 1px solid rgba(220, 38, 38, 0.25);">
                                <i class="bi bi-exclamation-octagon-fill text-danger fs-5"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline mb-1">
                            <h2 class="fw-bold mb-0 text-danger stat-number">{{ $unpaidBills }}</h2>
                        </div>
                        <div class="small text-muted pt-2 border-top border-light d-flex justify-content-between align-items-center">
                            <span>Pending collections</span>
                            <span class="text-danger fw-semibold"><i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('bills.index') }}" class="text-decoration-none d-block h-100" title="Click to view all billing records">
                <div class="card glass-card stat-glass-card clickable-card h-100" style="border-top: 3.5px solid #2563eb !important;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #1e3a8a;">Total Billed</span>
                            <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="background: rgba(37, 99, 235, 0.12); width: 40px; height: 40px;">
                                <i class="bi bi-cash-stack fs-5 text-primary"></i>
                            </div>
                        </div>
                        <div class="mb-1">
                            <h3 class="fw-bold text-dark mb-0 revenue-amount">₱{{ number_format($totalBilled, 2) }}</h3>
                        </div>
                        <div class="small text-muted pt-2 border-top border-light d-flex justify-content-between align-items-center">
                            <span>All accounts billed</span>
                            <span class="text-primary fw-semibold">View Bills <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('payments.index') }}" class="text-decoration-none d-block h-100" title="Click to view payment collections">
                <div class="card glass-card stat-glass-card clickable-card h-100" style="border-top: 3.5px solid #16a34a !important;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #15803d;">Total Collected</span>
                            <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="background: rgba(22, 163, 74, 0.12); width: 40px; height: 40px;">
                                <i class="bi bi-wallet2 fs-5 text-success"></i>
                            </div>
                        </div>
                        <div class="mb-1">
                            <h3 class="fw-bold text-success mb-0 revenue-amount">₱{{ number_format($totalCollected, 2) }}</h3>
                        </div>
                        <div class="small text-muted pt-2 border-top border-light d-flex justify-content-between align-items-center">
                            <span>Cash receipts settled</span>
                            <span class="text-success fw-semibold">View Payments <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('bills.index', ['status' => 'unpaid']) }}" class="text-decoration-none d-block h-100" title="Click to view uncollected receivables">
                <div class="card glass-card stat-glass-card clickable-card h-100" style="border-top: 3.5px solid #dc2626 !important;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #b91c1c;">Uncollected Receivables</span>
                            <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="background: rgba(220, 38, 38, 0.12); width: 40px; height: 40px;">
                                <i class="bi bi-clock-history fs-5 text-danger"></i>
                            </div>
                        </div>
                        <div class="mb-1">
                            <h3 class="fw-bold text-danger mb-0 revenue-amount">₱{{ number_format($outstandingBalance, 2) }}</h3>
                        </div>
                        <div class="small text-muted pt-2 border-top border-light d-flex justify-content-between align-items-center">
                            <span>Pending balances</span>
                            <span class="text-danger fw-semibold">View Unpaid <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('reports.index', ['report_type' => 'billing_collection']) }}" class="text-decoration-none d-block h-100" title="Click to view operational collection efficiency report">
                <div class="card glass-card stat-glass-card clickable-card h-100" style="border-top: 3.5px solid #0891b2 !important;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-bold text-uppercase" style="letter-spacing: 0.05em; color: #0e7490;">Collection Efficiency</span>
                            <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="background: rgba(8, 145, 178, 0.12); width: 40px; height: 40px;">
                                <i class="bi bi-pie-chart-fill fs-5" style="color: #0891b2;"></i>
                            </div>
                        </div>
                        <div class="mb-1">
                            <h3 class="fw-bold text-dark mb-0 revenue-amount">{{ $collectionRate }}%</h3>
                        </div>
                        <div class="small text-muted pt-2 border-top border-light d-flex justify-content-between align-items-center">
                            <span>Performance rate</span>
                            <span class="fw-semibold" style="color: #0891b2;">View Report <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-6">
            <div class="card glass-card h-100 overflow-hidden">
                <div class="card-header bg-transparent p-3 d-flex justify-content-between align-items-center" style="border-bottom: 1.5px solid #dbeafe;">
                    <h6 class="m-0 fw-bold text-dark d-flex align-items-center" style="font-family: var(--font-heading);">
                        <i class="bi bi-receipt text-primary me-2"></i> Recent Water Bills
                    </h6>
                    <a href="{{ route('bills.index') }}" class="small text-primary fw-bold text-decoration-none d-flex align-items-center gap-1">
                        View All <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                            <thead>
                                <tr>
                                    <th>Bill #</th>
                                    <th>Consumer</th>
                                    <th>Consumption</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBills as $b)
                                    <tr>
                                        <td class="font-monospace fw-bold text-primary">{{ $b->bill_number }}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $b->consumer->full_name }}</div>
                                            <small class="text-muted fw-semibold">{{ $b->consumer->account_number }}</small>
                                        </td>
                                        <td>{{ number_format($b->consumption, 1) }} m³</td>
                                        <td class="fw-bold">₱{{ number_format($b->amount_due, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $b->status === 'paid' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} text-uppercase rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                                {{ $b->status }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('bills.statement', $b) }}" target="_blank" class="btn btn-sm btn-glass-action py-1 px-2 rounded-2" title="Print Statement">
                                                <i class="bi bi-printer text-primary"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No bills recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card glass-card h-100 overflow-hidden">
                <div class="card-header bg-transparent p-3 d-flex justify-content-between align-items-center" style="border-bottom: 1.5px solid #dbeafe;">
                    <h6 class="m-0 fw-bold text-dark d-flex align-items-center" style="font-family: var(--font-heading);">
                        <i class="bi bi-wallet2 text-success me-2"></i> Recent OTC Collections
                    </h6>
                    <a href="{{ route('payments.index') }}" class="small text-primary fw-bold text-decoration-none d-flex align-items-center gap-1">
                        View All <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                            <thead>
                                <tr>
                                    <th>OR / Ref #</th>
                                    <th>Consumer</th>
                                    <th>Date</th>
                                    <th>Amount Paid</th>
                                    <th class="text-end">Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $p)
                                    <tr>
                                        <td class="font-monospace fw-bold text-success">{{ $p->reference_number }}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $p->consumer ? $p->consumer->full_name : ($p->bill->consumer->full_name ?? 'N/A') }}</div>
                                            <small class="text-muted fw-semibold">{{ $p->bill->bill_number }}</small>
                                        </td>
                                        <td>{{ $p->payment_date->format('M d, Y') }}</td>
                                        <td class="fw-bold text-success">₱{{ number_format($p->amount_paid, 2) }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('payments.receipt', $p) }}" target="_blank" class="btn btn-sm btn-glass-action py-1 px-2 rounded-2" title="Print OR">
                                                <i class="bi bi-file-earmark-check text-success"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No payments recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@stop
