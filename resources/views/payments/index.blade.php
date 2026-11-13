@extends('adminlte::page')

@section('title', 'Payment Records')

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <div>
            <h1 class="m-0 text-dark fw-bold" style="font-size: 1.75rem;">
                <i class="bi bi-cash-stack text-success me-2"></i>Payment Records
            </h1>
            <p class="text-muted small mb-0">Record physical over-the-counter payments and issue official receipts</p>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('payments.create') }}" class="btn btn-success btn-sm px-3 shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Record OTC Payment
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid px-0">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('payments.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by OR / reference #, bill #, or consumer..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="payment_method" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Payment Methods</option>
                        <option value="Cash" {{ request('payment_method') === 'Cash' ? 'selected' : '' }}>Cash Only</option>
                        <option value="Other" {{ request('payment_method') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-success flex-grow-1">Filter</button>
                    @if(request('search') || request('payment_method'))
                        <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th>OR / Reference #</th>
                        <th>Consumer</th>
                        <th>Bill #</th>
                        <th>Payment Date</th>
                        <th>Method</th>
                        <th>Amount Paid</th>
                        <th>Notes</th>
                        <th class="text-end">Official Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td class="font-monospace fw-bold text-success">
                                <a href="{{ route('payments.receipt', $payment) }}" target="_blank" class="text-decoration-none text-success">
                                    {{ $payment->reference_number }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">
                                    {{ $payment->consumer ? $payment->consumer->full_name : ($payment->bill->consumer->full_name ?? 'N/A') }}
                                </div>
                                <small class="text-muted">{{ $payment->consumer->account_number ?? ($payment->bill->consumer->account_number ?? '') }}</small>
                            </td>
                            <td class="font-monospace">{{ $payment->bill->bill_number }}</td>
                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $payment->payment_method }}</span></td>
                            <td class="fw-bold text-success">₱{{ number_format($payment->amount_paid, 2) }}</td>
                            <td><small class="text-muted">{{ $payment->notes ?? 'OTC Cash Payment' }}</small></td>
                            <td class="text-end">
                                <a href="{{ route('payments.receipt', $payment) }}" target="_blank" class="btn btn-sm btn-light" title="Print Official Receipt">
                                    <i class="bi bi-file-earmark-check text-success"></i> Print OR
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-wallet2 fs-3 d-block mb-2 text-secondary"></i>
                                No payment records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

</div>
@stop
